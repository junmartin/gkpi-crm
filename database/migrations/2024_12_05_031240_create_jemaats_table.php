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
        Schema::create('jemaats', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
            $table->tinyInteger('jenis_kelamin');
            $table->string('address');
            $table->string('birth_place');
            $table->date('birth_date');
            $table->string('mobile_no');
            $table->string('email');
            $table->string('marital_status');
            $table->date('marriage_date');
            $table->string('spouse_name');
            $table->string('member_type');
            $table->string('baptise_status');
            $table->string('previous_church')->nullable();
            $table->string('remark')->nullable();
            
            

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jemaats');
    }
};
