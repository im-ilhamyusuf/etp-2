<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PesertaJadwal extends Model
{
    protected $fillable = [
        'peserta_id',
        'jadwal_id',
        'bukti_bayar',
        'ajukan_bukti_bayar',
        'validasi_bukti_bayar',
        'sesi_soal',
        'batas_sesi',
    ];
}
