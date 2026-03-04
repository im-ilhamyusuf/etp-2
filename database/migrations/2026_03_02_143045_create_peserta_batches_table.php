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
        Schema::create('peserta_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peserta_id')->constrained()->cascadeOnDelete();
            $table->foreignId('batch_id')->constrained()->cascadeOnDelete();
            $table->string('bukti_bayar')->nullable();
            $table->dateTime('validasi')->nullable();
            $table->integer('poin_a1')->default(0);
            $table->integer('poin_b1')->default(0);
            $table->integer('poin_c1')->default(0);
            $table->float('nilai_akhir1')->default(0);
            $table->integer('poin_a2')->default(0);
            $table->integer('poin_b2')->default(0);
            $table->integer('poin_c2')->default(0);
            $table->float('nilai_akhir2')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peserta_batches');
    }
};
