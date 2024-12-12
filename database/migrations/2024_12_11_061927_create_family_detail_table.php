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
        Schema::create('family_detail', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('family_id');
            $table->foreign('family_id')->references('id')->on('family');
            $table->unsignedBigInteger('jemaat_id');
            $table->foreign('jemaat_id')->references('id')->on('jemaats');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('family_detail');        
    }
};
