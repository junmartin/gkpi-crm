<?php

use App\Support\FinanceTransactionCsvSync;
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
