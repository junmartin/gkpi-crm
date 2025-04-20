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
            $table->string('emergency_contact_name',100)->nullable()->change();
            $table->string('emergency_contact_mobile',100)->nullable()->change();
            $table->string('emergency_contact_relation',100)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jemaats', function (Blueprint $table) {
            //
        });
    }
};
