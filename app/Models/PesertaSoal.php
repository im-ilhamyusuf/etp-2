<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PesertaSoal extends Model
{
    protected $fillable = [
        'peserta_id',
        'jadwal_id',
        'soal_id',
        'soal_jawaban_id',
        'benar'
    ];
}
