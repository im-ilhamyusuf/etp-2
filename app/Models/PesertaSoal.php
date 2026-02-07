<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PesertaSoal extends Model
{
    protected $fillable = [
        'peserta_id',
        'jadwal_id',
        'bank_soal_id',
        'no',
        'soal_id',
        'soal_jawaban_id',
        'benar'
    ];

    public function bankSoal()
    {
        return $this->belongsTo(BankSoal::class);
    }

    public function soal()
    {
        return $this->belongsTo(Soal::class);
    }

    protected $appends = ['sudah'];

    public function getSudahAttribute()
    {
        return $this->created_at != $this->updated_at;
    }
}
