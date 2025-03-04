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
        Schema::table('assets', function (Blueprint $table) {
            $table->string('status')->nullable()->after('acquired_date');
            $table->string('location')->nullable()->after('status');
            $table->string('pic')->nullable()->after('location');
            $table->string('ownership')->nullable()->after('pic');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('location');
            $table->dropColumn('pic');
            $table->dropColumn('ownership');
        });
    }
};
