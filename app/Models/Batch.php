<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    protected $fillable = [
        'judul',
        'mulai',
        'tutup',
        'biaya_1',
        'biaya_2'
    ];
}
