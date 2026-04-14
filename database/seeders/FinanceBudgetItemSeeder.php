<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FinanceBudgetItemSeeder extends Seeder
{
    public function run(): void
    {
        $filePath = storage_path('budget-item.txt');

        if (!file_exists($filePath)) {
            $this->command->error("budget-item.txt not found at: $filePath");
            return;
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        // These are expense/budget items — mark them as routine if name has no "(NR)"
        $expenseItems = [];
        foreach ($lines as $line) {
            $parts = explode('|', $line, 2);
            if (count($parts) !== 2) continue;
            $name = trim($parts[0]);
            $annualAmount = (int) preg_replace('/[^0-9]/', '', trim($parts[1]));
            if ($name === '') continue;

            $isRoutine = !str_contains($name, '(NR)');
            $monthlyAmount = ($isRoutine && $annualAmount > 0) ? (int) round($annualAmount / 12) : null;

            $expenseItems[] = $name;
            DB::table('finance_budget_items')->insertOrIgnore([
                'name'                => $name,
                'is_active'           => true,
                'is_routine'          => $isRoutine,
                'routine_day_of_month'=> null,
                'routine_amount'      => $monthlyAmount,
                'notes'               => $annualAmount > 0 ? "Anggaran tahunan: Rp " . number_format($annualAmount, 0, ',', '.') : null,
                'create_by'           => null,
                'update_by'           => null,
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);
        }

        // Additional income/misc items found in transaction history not in the budget file
        $incomeItems = [
            'Persembahan Mingguan',
            'Persembahan Perpuluhan',
            'Persembahan SM',
            'Persembahan Diakonia',
            'Persembahan Lain-lain',
            'Persembahan Pembangunan',
            'Sumbangan Jemaat',
            'Sumbangan Pusat',
            'Pembulatan',
        ];

        foreach ($incomeItems as $name) {
            if (in_array($name, $expenseItems)) continue;
            DB::table('finance_budget_items')->insertOrIgnore([
                'name'                => $name,
                'is_active'           => true,
                'is_routine'          => false,
                'routine_day_of_month'=> null,
                'routine_amount'      => null,
                'notes'               => null,
                'create_by'           => null,
                'update_by'           => null,
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);
        }

        $count = DB::table('finance_budget_items')->count();
        $this->command->info("Finance budget items seeded. Total: $count");
    }
}
