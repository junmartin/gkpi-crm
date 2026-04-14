<?php

namespace Database\Seeders;

use App\Support\FinanceProductionData;
use Illuminate\Database\Seeder;

class FinanceProductionDataSeeder extends Seeder
{
    public function run(): void
    {
        $stats = app(FinanceProductionData::class)->import();

        if ($this->command) {
            $this->command->table(
                ['Dataset', 'Rows'],
                [
                    ['Accounts', $stats['accounts']],
                    ['Budget items', $stats['budget_items']],
                    ['Transactions', $stats['transactions']],
                    ['Monthly balances', $stats['monthly_balances']],
                ]
            );

            $this->command->info('Finance production data imported from ' . $stats['source_directory']);
        }
    }
}