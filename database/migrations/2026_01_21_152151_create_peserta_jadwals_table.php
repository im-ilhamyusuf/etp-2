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
        Schema::create('peserta_jadwals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peserta_id')->constrained()->cascadeOnDelete();
            $table->foreignId('jadwal_id')->constrained()->cascadeOnDelete();
            $table->string('bukti_bayar')->nullable();
            $table->dateTime('validasi')->nullable();
            $table->dateTime('mulai')->nullable();
            $table->dateTime('selesai')->nullable();
            $table->integer('sesi_soal')->default(0);
            $table->dateTime('batas_sesi')->nullable();
            $table->integer('poin_a')->default(0);
            $table->integer('poin_b')->default(0);
            $table->integer('poin_c')->default(0);
            $table->float('nilai_akhir')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peserta_jadwals');
    }
};
