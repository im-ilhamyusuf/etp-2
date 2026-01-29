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
        'selesai',
        'poin_a',
        'poin_b',
        'poin_c',
        'nilai_akhir',
    ];

    protected $casts = [
        'selesai' => 'datetime'
    ];

    public function peserta()
    {
        return $this->belongsTo(Peserta::class);
    }

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class);
    }

    protected $appends = ['statusUjian', 'statusUjianColor'];

    public function getStatusUjianAttribute()
    {
        if ($this->mulai == null) {
            return 'Belum Dimulai';
        }

        if ($this->mulai != null && $this->selesai == null) {
            return 'Sedang Ujian';
        }

        if ($this->mulai != null && $this->selesai != null) {
            return 'Sudah Selesai';
        }
    }

    public function getStatusUjianColorAttribute()
    {
        if ($this->mulai == null) {
            return 'danger';
        }

        if ($this->mulai != null && $this->selesai == null) {
            return 'warning';
        }

        if ($this->mulai != null && $this->selesai != null) {
            return 'success';
        }
    }
}
