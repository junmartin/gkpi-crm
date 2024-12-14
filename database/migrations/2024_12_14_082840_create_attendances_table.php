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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->date('sermon_date');
            $table->unsignedBigInteger('ibadah_id');
            $table->foreign('ibadah_id')->references('id')->on('ibadahs');
            $table->string('ibadah_name');
            $table->unsignedBigInteger('jemaat_id');
            $table->foreign('jemaat_id')->references('id')->on('jemaats');
            $table->tinyInteger('attendance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
