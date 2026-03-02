<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankSoal extends Model
{
    protected $fillable = ['jenis', 'sesi', 'judul', 'gambar', 'audio'];

    public function soal()
    {
        return $this->hasMany(Soal::class, "bank_soal_id", "id");
    }
}
