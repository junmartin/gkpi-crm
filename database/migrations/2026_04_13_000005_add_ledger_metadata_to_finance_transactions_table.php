<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('finance_transactions', function (Blueprint $table) {
            $table->string('entry_type', 30)->default('regular')->after('amount')->index();
            $table->string('transfer_group_key', 64)->nullable()->after('entry_type')->index();
            $table->string('import_fingerprint', 64)->nullable()->after('transfer_group_key')->index();
        });

        DB::table('finance_transactions as t')
            ->leftJoin('finance_budget_items as b', 'b.id', '=', 't.budget_item_id')
            ->whereRaw("LOWER(TRIM(COALESCE(b.name, ''))) = ?", ['<< internal transfer >>'])
            ->update(['entry_type' => 'internal_transfer']);

        DB::table('finance_transactions as t')
            ->leftJoin('finance_budget_items as b', 'b.id', '=', 't.budget_item_id')
            ->whereRaw("LOWER(TRIM(COALESCE(b.name, ''))) = ?", ['<< opening balance >>'])
            ->update(['entry_type' => 'opening_balance']);
    }

    public function down(): void
    {
        Schema::table('finance_transactions', function (Blueprint $table) {
            $table->dropIndex(['entry_type']);
            $table->dropIndex(['transfer_group_key']);
            $table->dropIndex(['import_fingerprint']);
            $table->dropColumn(['entry_type', 'transfer_group_key', 'import_fingerprint']);
        });
    }
};