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
        Schema::table('jemaats', function (Blueprint $table) {
            $table->string('emergency_contact_name',100)->after('spouse_name');
            $table->string('emergency_contact_mobile',100)->after('emergency_contact_name');
            $table->string('emergency_contact_relation',100)->after('emergency_contact_mobile');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jemaats', function (Blueprint $table) {
            $table->dropColumn('emergency_contact_name');
            $table->dropColumn('emergency_contact_mobile');
            $table->dropColumn('emergency_contact_relation');
        });
    }
};
