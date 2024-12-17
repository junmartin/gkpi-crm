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
        Schema::create('sermon_attendances', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('sermon_id');
            $table->foreign('sermon_id')->references('id')->on('sermons');
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
        Schema::dropIfExists('sermon_attendances');
    }
};
