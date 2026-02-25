<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Materi extends Model
{
    protected $fillable = ['jenis', 'judul', 'url_video', 'url_ujian_1', 'url_ujian_2'];
}
