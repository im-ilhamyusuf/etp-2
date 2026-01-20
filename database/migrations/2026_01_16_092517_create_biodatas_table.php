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
        Schema::create('biodatas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('nik', 20)->unique();
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->enum('agama', ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu']);
            $table->enum('pendidikan_terakhir', ['Tidak Tamat SD', 'SD', 'SMP', 'SMA/SMK', 'D3', 'S1', 'S2', 'S3']);
            $table->year('tahun_lulus')->nullable();
            $table->string('pekerjaan')->nullable();
            $table->string('instansi')->nullable();
            $table->string('nim')->nullable();
            $table->string('nidn')->nullable();
            $table->enum('kewarganegaraan', ['WNI', 'WNA']);
            $table->string('bahasa')->nullable();
            $table->string('no_hp', 20);
            $table->text('alamat');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biodatas');
    }
};
