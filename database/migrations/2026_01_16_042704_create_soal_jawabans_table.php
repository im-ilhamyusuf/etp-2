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
        Schema::create('soal_jawabans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('soal_id')->constrained('soals')->cascadeOnDelete();
            $table->string('jawaban');
            $table->boolean('benar');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soal_jawabans');
    }
};
