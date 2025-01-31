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
            $table->unsignedBigInteger('create_by')->nullable();
            $table->foreign('create_by')->references('id')->on('users');
            $table->unsignedBigInteger('update_by')->nullable();
            $table->foreign('update_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jemaats', function (Blueprint $table) {
            $table->dropColumn('create_by');
            $table->dropColumn('update_by');
        });
    }
};
