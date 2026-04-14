<?php

use App\Support\FinanceProductionData;
use App\Support\FinanceTransactionCsvSync;
use Database\Seeders\FinanceProductionDataSeeder;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('finance:sync-csv {path : Absolute path to finance CSV} {--prune-unmatched : Delete existing rows not found in CSV}', function (string $path) {
    $stats = app(FinanceTransactionCsvSync::class)->sync($path, null, (bool) $this->option('prune-unmatched'));

    $this->table(
        ['Metric', 'Value'],
        collect($stats)->map(fn ($value, $key) => [str_replace('_', ' ', $key), $value])->all()
    );
})->purpose('Sync finance transactions from a signed-amount CSV into finance_transactions');

Artisan::command('finance:export-production-data', function () {
    $stats = app(FinanceProductionData::class)->export();

    $this->table(
        ['Dataset', 'Rows'],
        [
            ['Accounts', $stats['accounts']],
            ['Budget items', $stats['budget_items']],
            ['Transactions', $stats['transactions']],
            ['Monthly balances', $stats['monthly_balances']],
        ]
    );

    $this->info('Finance production data exported to ' . $stats['output_directory']);
})->purpose('Export the current finance master and transaction data into JSON seed files for production');

Artisan::command('finance:import-production-data', function () {
    Artisan::call('db:seed', [
        '--class' => FinanceProductionDataSeeder::class,
        '--force' => true,
    ]);

    $this->output->write(Artisan::output());
})->purpose('Import finance account master, budget items, transactions, and monthly balances into the current environment');
