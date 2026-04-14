<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('finance_transactions', function (Blueprint $table) {
            $table->id();
            $table->date('trx_date')->index();
            $table->enum('transaction_type', ['income', 'expense'])->default('expense');
            $table->enum('account', ['cash', 'bank'])->default('cash')->index();
            $table->foreignId('budget_item_id')->constrained('finance_budget_items')->cascadeOnUpdate()->restrictOnDelete();
            $table->unsignedBigInteger('amount');
            $table->text('description')->nullable();
            $table->string('project')->nullable();
            $table->string('attachment_path')->nullable();
            $table->foreignId('create_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('update_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['trx_date', 'transaction_type']);
            $table->index(['trx_date', 'budget_item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finance_transactions');
    }
};
