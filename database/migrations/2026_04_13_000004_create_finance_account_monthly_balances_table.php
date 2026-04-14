<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('finance_account_monthly_balances', function (Blueprint $table) {
            $table->id();
            $table->string('account', 100);
            $table->date('period_month');
            $table->bigInteger('opening_balance')->default(0);
            $table->bigInteger('inflow_amount')->default(0);
            $table->bigInteger('outflow_amount')->default(0);
            $table->bigInteger('closing_balance')->default(0);
            $table->unsignedBigInteger('create_by')->nullable();
            $table->unsignedBigInteger('update_by')->nullable();
            $table->timestamps();

            $table->unique(['account', 'period_month'], 'finance_account_monthly_balances_account_month_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_account_monthly_balances');
    }
};
