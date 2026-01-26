<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PesertaJadwal extends Model
{
    protected $fillable = [
        'peserta_id',
        'jadwal_id',
        'bukti_bayar',
        'validasi',
        'sesi_soal',
        'batas_sesi',
        'mulai',
        'selesai'
    ];

    public function peserta()
    {
        return $this->belongsTo(Peserta::class);
    }

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class);
    }
}
