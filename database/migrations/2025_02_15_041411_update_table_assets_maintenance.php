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
        Schema::table('assets_maintenance', function (Blueprint $table) {
            $table->string('masalah',500)->nullable()->after('desc');
            $table->string('diagnosa',1000)->nullable()->after('masalah');
            $table->string('tindakan',1000)->nullable()->after('diagnosa');
            $table->string('hasil',1000)->nullable()->after('tindakan');
            $table->string('vendor_name',100)->nullable()->after('maint_title');
            $table->string('vendor_address',500)->nullable()->after('vendor_name');
            $table->string('vendor_contact',100)->nullable()->after('vendor_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets_maintenance', function (Blueprint $table) {
            $table->dropColumn('masalah');
            $table->dropColumn('diagnosa');
            $table->dropColumn('tindakan');
            $table->dropColumn('hasil');
            $table->dropColumn('vendor_name');
            $table->dropColumn('vendor_address');
            $table->dropColumn('vendor_contact');
        });
    }
};
