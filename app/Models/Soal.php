<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Soal extends Model
{
    protected $fillable = [
        'bank_soal_id',
        'soal',
        'gambar',
        'audio'
    ];

    public function bankSoal()
    {
        return $this->belongsTo(related: BankSoal::class);
    }

    public function soalJawaban()
    {
        return $this->hasMany(SoalJawaban::class);
    }
}
