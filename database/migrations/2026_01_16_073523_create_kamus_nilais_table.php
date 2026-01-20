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
        Schema::create('kamus_nilais', function (Blueprint $table) {
            $table->id();
            $table->integer('jumlah_benar');
            $table->integer('listening');
            $table->integer('reading');
            $table->integer('structure');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kamus_nilais');
    }
};
