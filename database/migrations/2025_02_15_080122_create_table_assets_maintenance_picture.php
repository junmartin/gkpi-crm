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
        Schema::create('assets_maintenance_picture', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('asset_maint_id');
            $table->foreign('asset_maint_id')->references('id')->on('assets_maintenance');
            $table->string('asset_photo',500);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets_maintenance_picture');
    }
};
