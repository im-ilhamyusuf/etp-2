<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SoalJawaban extends Model
{
    protected $fillable = [
        'soal_id',
        'jawaban',
        'benar'
    ];

    public function soal()
    {
        return $this->belongsTo(Soal::class);
    }
}
