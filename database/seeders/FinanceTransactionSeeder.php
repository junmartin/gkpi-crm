<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FinanceTransactionSeeder extends Seeder
{
    public function run(): void
    {
        $filePath = storage_path('keuangan-trx.csv');

        if (!file_exists($filePath)) {
            $this->command->error("keuangan-trx.csv not found at: $filePath");
            return;
        }

        // Build budget_item lookup: name => id
        $budgetItems = DB::table('finance_budget_items')
            ->pluck('id', 'name')
            ->toArray();

        $handle = fopen($filePath, 'r');
        if (!$handle) {
            $this->command->error('Could not open keuangan-trx.csv');
            return;
        }

        $imported  = 0;
        $skipped   = 0;
        $missing   = [];
        $batch     = [];
        $now       = now()->toDateTimeString();

        while (($row = fgetcsv($handle)) !== false) {
            // Skip header row and blank rows
            if (empty($row[0]) || $row[0] === 'Datetime') continue;

            $rawDate   = trim($row[0] ?? '');
            $acct      = trim($row[1] ?? '');
            $itemName  = trim($row[2] ?? '');
            $rawAmount = trim($row[3] ?? '');
            $desc      = trim($row[4] ?? '') ?: null;
            $project   = trim($row[5] ?? '') ?: null;

            // Skip internal transfers
            if ($itemName === '<< INTERNAL TRANSFER >>' || $itemName === '') continue;

            // Parse date (M/D/YYYY)
            $dateParsed = date_create_from_format('n/j/Y', $rawDate);
            if (!$dateParsed) {
                $skipped++;
                continue;
            }
            $trxDate = $dateParsed->format('Y-m-d');

            // Parse amount: remove commas, handle negatives
            $cleanAmount = str_replace([',', ' '], '', $rawAmount);
            $amount      = (float) $cleanAmount;
            $trxType     = $amount >= 0 ? 'income' : 'expense';
            $amountAbs   = (int) abs($amount);

            if ($amountAbs === 0) {
                $skipped++;
                continue;
            }

            // Resolve budget item id
            if (!isset($budgetItems[$itemName])) {
                $missing[$itemName] = true;
                $skipped++;
                continue;
            }
            $budgetItemId = $budgetItems[$itemName];

            $batch[] = [
                'trx_date'        => $trxDate,
                'transaction_type'=> $trxType,
                'account'         => $acct,
                'budget_item_id'  => $budgetItemId,
                'amount'          => $amountAbs,
                'description'     => $desc,
                'project'         => $project,
                'attachment_path' => null,
                'create_by'       => null,
                'update_by'       => null,
                'created_at'      => $now,
                'updated_at'      => $now,
            ];
            $imported++;

            // Insert in batches of 100
            if (count($batch) >= 100) {
                DB::table('finance_transactions')->insert($batch);
                $batch = [];
            }
        }

        fclose($handle);

        if (!empty($batch)) {
            DB::table('finance_transactions')->insert($batch);
        }

        $this->command->info("Imported: $imported transactions.");

        if ($skipped > 0) {
            $this->command->warn("Skipped: $skipped rows.");
        }

        if (!empty($missing)) {
            $this->command->warn('Missing budget items (transactions skipped):');
            foreach (array_keys($missing) as $m) {
                $this->command->warn("  - $m");
            }
        }
    }
}
