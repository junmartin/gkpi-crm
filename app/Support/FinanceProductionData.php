<?php

namespace App\Support;

use App\Models\FinanceAccount;
use App\Models\FinanceAccountMonthlyBalance;
use App\Models\FinanceBudgetItem;
use App\Models\FinanceTransaction;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class FinanceProductionData
{
    private const DATA_DIRECTORY = 'database/seeders/data';

    private const ACCOUNTS_FILE = 'finance_accounts.json';

    private const BUDGET_ITEMS_FILE = 'finance_budget_items.json';

    private const TRANSACTIONS_FILE = 'finance_transactions.json';

    private const MONTHLY_BALANCES_FILE = 'finance_account_monthly_balances.json';

    private const DEFAULT_ACCOUNT_NAMES = [
        'BCA-0539',
        'Kas Alfret',
        'Kas Anne',
        'Kas Diana',
        'Kas Jun',
        'Kas Kimsen',
        'Kas Lili',
        'Kas Luna',
        'Payable',
    ];

    public function export(): array
    {
        File::ensureDirectoryExists($this->dataDirectory());

        $datasets = [
            self::ACCOUNTS_FILE => $this->exportAccounts(),
            self::BUDGET_ITEMS_FILE => $this->exportBudgetItems(),
            self::TRANSACTIONS_FILE => $this->exportTransactions(),
            self::MONTHLY_BALANCES_FILE => $this->exportMonthlyBalances(),
        ];

        foreach ($datasets as $fileName => $rows) {
            $encoded = json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

            if ($encoded === false) {
                throw new RuntimeException("Unable to encode dataset: {$fileName}");
            }

            File::put($this->dataPath($fileName), $encoded . PHP_EOL);
        }

        return [
            'accounts' => count($datasets[self::ACCOUNTS_FILE]),
            'budget_items' => count($datasets[self::BUDGET_ITEMS_FILE]),
            'transactions' => count($datasets[self::TRANSACTIONS_FILE]),
            'monthly_balances' => count($datasets[self::MONTHLY_BALANCES_FILE]),
            'output_directory' => self::DATA_DIRECTORY,
        ];
    }

    public function import(): array
    {
        $accounts = $this->readDataset(self::ACCOUNTS_FILE);
        $budgetItems = $this->readDataset(self::BUDGET_ITEMS_FILE);
        $transactions = $this->readDataset(self::TRANSACTIONS_FILE);
        $monthlyBalances = $this->readDataset(self::MONTHLY_BALANCES_FILE);

        DB::transaction(function () use ($accounts, $budgetItems, $transactions, $monthlyBalances) {
            $this->upsertAccounts($accounts);
            $this->upsertBudgetItems($budgetItems);
            $this->upsertTransactions($transactions);
            $this->upsertMonthlyBalances($monthlyBalances);
        });

        return [
            'accounts' => count($accounts),
            'budget_items' => count($budgetItems),
            'transactions' => count($transactions),
            'monthly_balances' => count($monthlyBalances),
            'source_directory' => self::DATA_DIRECTORY,
        ];
    }

    public static function defaultAccountNames(): array
    {
        return self::DEFAULT_ACCOUNT_NAMES;
    }

    private function exportAccounts(): array
    {
        $accountNames = collect(self::DEFAULT_ACCOUNT_NAMES);

        if (Schema::hasTable('finance_accounts') && FinanceAccount::query()->exists()) {
            return FinanceAccount::query()
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get([
                    'name',
                    'is_active',
                    'sort_order',
                    'notes',
                    'create_by',
                    'update_by',
                    'created_at',
                    'updated_at',
                ])
                ->map(fn (FinanceAccount $account) => $this->serializeRow($account->getAttributes(), ['created_at', 'updated_at']))
                ->all();
        }

        if (Schema::hasTable('finance_transactions')) {
            $accountNames = $accountNames
                ->merge(
                    FinanceTransaction::query()
                        ->select('account')
                        ->distinct()
                        ->orderBy('account')
                        ->pluck('account')
                )
                ->filter()
                ->unique()
                ->values();
        }

        return $accountNames
            ->values()
            ->map(fn (string $name, int $index) => [
                'name' => $name,
                'is_active' => true,
                'sort_order' => $index + 1,
                'notes' => null,
                'create_by' => null,
                'update_by' => null,
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ])
            ->all();
    }

    private function exportBudgetItems(): array
    {
        return FinanceBudgetItem::query()
            ->orderBy('id')
            ->get([
                'id',
                'name',
                'is_active',
                'is_routine',
                'routine_day_of_month',
                'routine_amount',
                'notes',
                'create_by',
                'update_by',
                'created_at',
                'updated_at',
            ])
            ->map(fn (FinanceBudgetItem $item) => $this->serializeRow($item->getAttributes(), ['created_at', 'updated_at']))
            ->all();
    }

    private function exportTransactions(): array
    {
        return FinanceTransaction::query()
            ->orderBy('id')
            ->get([
                'id',
                'trx_date',
                'transaction_type',
                'account',
                'budget_item_id',
                'amount',
                'entry_type',
                'transfer_group_key',
                'import_fingerprint',
                'description',
                'project',
                'attachment_path',
                'create_by',
                'update_by',
                'created_at',
                'updated_at',
            ])
            ->map(fn (FinanceTransaction $transaction) => $this->serializeRow($transaction->getAttributes(), ['trx_date', 'created_at', 'updated_at']))
            ->all();
    }

    private function exportMonthlyBalances(): array
    {
        return FinanceAccountMonthlyBalance::query()
            ->orderBy('period_month')
            ->orderBy('account')
            ->get([
                'id',
                'account',
                'period_month',
                'opening_balance',
                'inflow_amount',
                'outflow_amount',
                'closing_balance',
                'create_by',
                'update_by',
                'created_at',
                'updated_at',
            ])
            ->map(fn (FinanceAccountMonthlyBalance $balance) => $this->serializeRow($balance->getAttributes(), ['period_month', 'created_at', 'updated_at']))
            ->all();
    }

    private function upsertAccounts(array $rows): void
    {
        $payload = collect($rows)
            ->map(function (array $row) {
                return [
                    'name' => (string) $row['name'],
                    'is_active' => (bool) ($row['is_active'] ?? true),
                    'sort_order' => (int) ($row['sort_order'] ?? 0),
                    'notes' => $row['notes'] ?? null,
                    'create_by' => $row['create_by'] ?? null,
                    'update_by' => $row['update_by'] ?? null,
                    'created_at' => $row['created_at'] ?? now()->toDateTimeString(),
                    'updated_at' => $row['updated_at'] ?? now()->toDateTimeString(),
                ];
            })
            ->all();

        FinanceAccount::query()->upsert(
            $payload,
            ['name'],
            ['is_active', 'sort_order', 'notes', 'create_by', 'update_by', 'created_at', 'updated_at']
        );
    }

    private function upsertBudgetItems(array $rows): void
    {
        $payload = collect($rows)
            ->map(function (array $row) {
                return [
                    'id' => (int) $row['id'],
                    'name' => (string) $row['name'],
                    'is_active' => (bool) ($row['is_active'] ?? true),
                    'is_routine' => (bool) ($row['is_routine'] ?? false),
                    'routine_day_of_month' => $row['routine_day_of_month'] ?? null,
                    'routine_amount' => $row['routine_amount'] ?? null,
                    'notes' => $row['notes'] ?? null,
                    'create_by' => $row['create_by'] ?? null,
                    'update_by' => $row['update_by'] ?? null,
                    'created_at' => $row['created_at'] ?? now()->toDateTimeString(),
                    'updated_at' => $row['updated_at'] ?? now()->toDateTimeString(),
                ];
            });

        $this->upsertInChunks('finance_budget_items', $payload, ['id'], [
            'name',
            'is_active',
            'is_routine',
            'routine_day_of_month',
            'routine_amount',
            'notes',
            'create_by',
            'update_by',
            'created_at',
            'updated_at',
        ]);
    }

    private function upsertTransactions(array $rows): void
    {
        $payload = collect($rows)
            ->map(function (array $row) {
                return [
                    'id' => (int) $row['id'],
                    'trx_date' => (string) $row['trx_date'],
                    'transaction_type' => (string) $row['transaction_type'],
                    'account' => (string) $row['account'],
                    'budget_item_id' => (int) $row['budget_item_id'],
                    'amount' => (int) $row['amount'],
                    'entry_type' => $row['entry_type'] ?? FinanceTransaction::ENTRY_TYPE_REGULAR,
                    'transfer_group_key' => $row['transfer_group_key'] ?? null,
                    'import_fingerprint' => $row['import_fingerprint'] ?? null,
                    'description' => $row['description'] ?? null,
                    'project' => $row['project'] ?? null,
                    'attachment_path' => $row['attachment_path'] ?? null,
                    'create_by' => $row['create_by'] ?? null,
                    'update_by' => $row['update_by'] ?? null,
                    'created_at' => $row['created_at'] ?? now()->toDateTimeString(),
                    'updated_at' => $row['updated_at'] ?? now()->toDateTimeString(),
                ];
            });

        $this->upsertInChunks('finance_transactions', $payload, ['id'], [
            'trx_date',
            'transaction_type',
            'account',
            'budget_item_id',
            'amount',
            'entry_type',
            'transfer_group_key',
            'import_fingerprint',
            'description',
            'project',
            'attachment_path',
            'create_by',
            'update_by',
            'created_at',
            'updated_at',
        ]);
    }

    private function upsertMonthlyBalances(array $rows): void
    {
        $payload = collect($rows)
            ->map(function (array $row) {
                return [
                    'id' => (int) $row['id'],
                    'account' => (string) $row['account'],
                    'period_month' => (string) $row['period_month'],
                    'opening_balance' => (int) $row['opening_balance'],
                    'inflow_amount' => (int) $row['inflow_amount'],
                    'outflow_amount' => (int) $row['outflow_amount'],
                    'closing_balance' => (int) $row['closing_balance'],
                    'create_by' => $row['create_by'] ?? null,
                    'update_by' => $row['update_by'] ?? null,
                    'created_at' => $row['created_at'] ?? now()->toDateTimeString(),
                    'updated_at' => $row['updated_at'] ?? now()->toDateTimeString(),
                ];
            });

        $this->upsertInChunks('finance_account_monthly_balances', $payload, ['id'], [
            'account',
            'period_month',
            'opening_balance',
            'inflow_amount',
            'outflow_amount',
            'closing_balance',
            'create_by',
            'update_by',
            'created_at',
            'updated_at',
        ]);
    }

    private function upsertInChunks(string $table, Collection $rows, array $uniqueBy, array $updateColumns): void
    {
        $rows
            ->chunk(200)
            ->each(function (Collection $chunk) use ($table, $uniqueBy, $updateColumns) {
                DB::table($table)->upsert($chunk->all(), $uniqueBy, $updateColumns);
            });
    }

    private function readDataset(string $fileName): array
    {
        $path = $this->dataPath($fileName);

        if (!File::exists($path)) {
            throw new RuntimeException("Production finance dataset not found: {$path}");
        }

        $decoded = json_decode((string) File::get($path), true);

        if (!is_array($decoded)) {
            throw new RuntimeException("Production finance dataset is invalid JSON: {$path}");
        }

        return $decoded;
    }

    private function serializeRow(array $attributes, array $dateKeys = []): array
    {
        foreach ($dateKeys as $key) {
            if (!isset($attributes[$key]) || $attributes[$key] === null) {
                continue;
            }

            $attributes[$key] = (string) $attributes[$key];
        }

        return $attributes;
    }

    private function dataDirectory(): string
    {
        return base_path(self::DATA_DIRECTORY);
    }

    private function dataPath(string $fileName): string
    {
        return $this->dataDirectory() . DIRECTORY_SEPARATOR . $fileName;
    }
}