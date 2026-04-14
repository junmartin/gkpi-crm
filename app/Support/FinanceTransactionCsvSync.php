<?php

namespace App\Support;

use App\Models\FinanceBudgetItem;
use App\Models\FinanceTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class FinanceTransactionCsvSync
{
    public function sync(string $csvPath, ?int $userId = null, bool $pruneUnmatched = false): array
    {
        if (!is_file($csvPath)) {
            throw new RuntimeException("CSV file not found: {$csvPath}");
        }

        $parsedRows = $this->parseCsv($csvPath);
        $budgetItemIds = $this->ensureBudgetItems($parsedRows['rows']);
        $existingBuckets = $this->loadExistingBuckets();

        $stats = [
            'total_rows' => count($parsedRows['rows']),
            'created' => 0,
            'updated' => 0,
            'matched' => 0,
            'skipped_empty' => $parsedRows['skipped_empty'],
            'skipped_invalid' => $parsedRows['skipped_invalid'],
        ];

        DB::transaction(function () use (&$stats, $parsedRows, $budgetItemIds, &$existingBuckets, $userId) {
            foreach ($parsedRows['rows'] as $row) {
                $payload = [
                    'trx_date' => $row['trx_date'],
                    'transaction_type' => $row['transaction_type'],
                    'account' => $row['account'],
                    'budget_item_id' => $budgetItemIds[$row['budget_item_name']],
                    'amount' => $row['amount'],
                    'entry_type' => $row['entry_type'],
                    'transfer_group_key' => $row['transfer_group_key'],
                    'import_fingerprint' => $row['import_fingerprint'],
                    'description' => $row['description'],
                    'project' => $row['project'],
                    'update_by' => $userId,
                ];

                $matched = null;
                if (!empty($existingBuckets[$row['import_fingerprint']])) {
                    $matched = array_shift($existingBuckets[$row['import_fingerprint']]);
                }

                if ($matched) {
                    $matched->fill($payload);
                    if ($matched->isDirty()) {
                        $matched->save();
                        $stats['updated']++;
                    } else {
                        $stats['matched']++;
                    }
                    continue;
                }

                FinanceTransaction::create($payload + [
                    'attachment_path' => null,
                    'create_by' => $userId,
                ]);
                $stats['created']++;
            }
        });

        $unmatched = array_sum(array_map('count', $existingBuckets));
        $stats['unmatched_existing'] = $unmatched;

        if ($pruneUnmatched && $unmatched > 0) {
            $idsToDelete = collect($existingBuckets)
                ->flatten(1)
                ->map(fn (FinanceTransaction $row) => $row->id)
                ->values()
                ->all();

            if (!empty($idsToDelete)) {
                FinanceTransaction::query()->whereIn('id', $idsToDelete)->delete();
                $stats['pruned'] = count($idsToDelete);
                $stats['unmatched_existing'] = 0;
            }
        }

        return $stats;
    }

    private function parseCsv(string $csvPath): array
    {
        $handle = fopen($csvPath, 'r');
        if ($handle === false) {
            throw new RuntimeException("Unable to open CSV file: {$csvPath}");
        }

        $header = fgetcsv($handle);
        if ($header === false) {
            fclose($handle);
            throw new RuntimeException('CSV file is empty.');
        }

        $rows = [];
        $skippedEmpty = 0;
        $skippedInvalid = 0;
        $transferCounters = [];

        while (($data = fgetcsv($handle)) !== false) {
            $mapped = $this->mapCsvRow($header, $data);
            if ($this->isEmptyCsvRow($mapped)) {
                $skippedEmpty++;
                continue;
            }

            if ($this->isInvalidCsvRow($mapped)) {
                $skippedInvalid++;
                continue;
            }

            $trxDate = $this->parseDate($mapped['Datetime']);
            $account = trim((string) $mapped['Acct']);
            $item = trim((string) $mapped['Item']);
            $description = $this->normalizeNullableText($mapped['Description']);
            $project = $this->normalizeNullableText($mapped['Project']);
            $signedAmount = $this->parseSignedAmount($mapped['Amount']);
            $transactionType = $signedAmount >= 0 ? 'income' : 'expense';
            $amount = abs($signedAmount);
            $entryType = FinanceTransaction::ENTRY_TYPE_REGULAR;
            $budgetItemName = $item;
            $transferGroupKey = null;

            if (strcasecmp($item, '<< INTERNAL TRANSFER >>') === 0) {
                $entryType = FinanceTransaction::ENTRY_TYPE_INTERNAL_TRANSFER;
                $budgetItemName = '<< INTERNAL TRANSFER >>';
                $pairSignature = implode('|', [
                    $trxDate,
                    $amount,
                    $this->normalizeFingerprintPart($description),
                    $this->normalizeFingerprintPart($project),
                ]);
                $transferCounters[$pairSignature] = ($transferCounters[$pairSignature] ?? 0) + 1;
                $pairIndex = (int) ceil($transferCounters[$pairSignature] / 2);
                $transferGroupKey = sha1($pairSignature . '|' . $pairIndex);
            } elseif ($item === '') {
                $entryType = FinanceTransaction::ENTRY_TYPE_OPENING_BALANCE;
                $budgetItemName = '<< OPENING BALANCE >>';
            }

            $rows[] = [
                'trx_date' => $trxDate,
                'account' => $account,
                'budget_item_name' => $budgetItemName,
                'transaction_type' => $transactionType,
                'amount' => $amount,
                'entry_type' => $entryType,
                'transfer_group_key' => $transferGroupKey,
                'description' => $description,
                'project' => $project,
                'import_fingerprint' => $this->buildFingerprint([
                    $trxDate,
                    $account,
                    $budgetItemName,
                    $transactionType,
                    $amount,
                    $entryType,
                ]),
            ];
        }

        fclose($handle);

        return [
            'rows' => $rows,
            'skipped_empty' => $skippedEmpty,
            'skipped_invalid' => $skippedInvalid,
        ];
    }

    private function loadExistingBuckets(): array
    {
        $buckets = [];

        FinanceTransaction::with('budgetItem')
            ->orderBy('id')
            ->get()
            ->each(function (FinanceTransaction $transaction) use (&$buckets) {
                $fingerprint = $transaction->import_fingerprint ?: $this->buildFingerprint([
                    optional($transaction->trx_date)->toDateString(),
                    $transaction->account,
                    $this->budgetItemNameForTransaction($transaction),
                    $transaction->transaction_type,
                    (int) $transaction->amount,
                    $transaction->entry_type ?: $this->inferEntryType($transaction),
                ]);

                $buckets[$fingerprint] ??= [];
                $buckets[$fingerprint][] = $transaction;
            });

        return $buckets;
    }

    private function ensureBudgetItems(array $rows): array
    {
        $names = collect($rows)->pluck('budget_item_name')->unique()->values();
        $ids = [];

        foreach ($names as $name) {
            $defaults = ['is_active' => true, 'is_routine' => false];
            if ($name === '<< OPENING BALANCE >>') {
                $defaults['is_active'] = false;
            }

            $ids[$name] = FinanceBudgetItem::firstOrCreate(['name' => $name], $defaults)->id;
        }

        return $ids;
    }

    private function mapCsvRow(array $header, array $data): array
    {
        $mapped = [];
        foreach ($header as $index => $key) {
            $mapped[$key] = $data[$index] ?? null;
        }

        return $mapped;
    }

    private function isEmptyCsvRow(array $row): bool
    {
        return $this->normalizeNullableText($row['Datetime'] ?? null) === null
            && $this->normalizeNullableText($row['Acct'] ?? null) === null
            && $this->normalizeNullableText($row['Item'] ?? null) === null
            && $this->normalizeNullableText($row['Amount'] ?? null) === null
            && $this->normalizeNullableText($row['Description'] ?? null) === null
            && $this->normalizeNullableText($row['Project'] ?? null) === null;
    }

    private function isInvalidCsvRow(array $row): bool
    {
        return $this->normalizeNullableText($row['Datetime'] ?? null) === null
            || $this->normalizeNullableText($row['Acct'] ?? null) === null
            || $this->normalizeNullableText($row['Amount'] ?? null) === null;
    }

    private function parseDate(string $value): string
    {
        return Carbon::createFromFormat('n/j/Y', trim($value))->toDateString();
    }

    private function parseSignedAmount(string $value): int
    {
        return (int) round((float) str_replace(',', '', trim($value)));
    }

    private function normalizeNullableText(?string $value): ?string
    {
        $value = trim((string) $value);
        return $value === '' ? null : preg_replace('/\s+/', ' ', $value);
    }

    private function normalizeFingerprintPart(?string $value): string
    {
        return strtolower((string) $this->normalizeNullableText($value));
    }

    private function buildFingerprint(array $parts): string
    {
        $normalized = array_map(function ($value) {
            if ($value === null) {
                return '';
            }

            return strtolower(trim(preg_replace('/\s+/', ' ', (string) $value)));
        }, $parts);

        return sha1(implode('|', $normalized));
    }

    private function inferEntryType(FinanceTransaction $transaction): string
    {
        if ($transaction->isOpeningBalance()) {
            return FinanceTransaction::ENTRY_TYPE_OPENING_BALANCE;
        }

        if ($transaction->isInternalTransfer()) {
            return FinanceTransaction::ENTRY_TYPE_INTERNAL_TRANSFER;
        }

        return FinanceTransaction::ENTRY_TYPE_REGULAR;
    }

    private function budgetItemNameForTransaction(FinanceTransaction $transaction): string
    {
        if ($transaction->isOpeningBalance()) {
            return '<< OPENING BALANCE >>';
        }

        if ($transaction->isInternalTransfer()) {
            return '<< INTERNAL TRANSFER >>';
        }

        return trim((string) optional($transaction->budgetItem)->name);
    }
}
