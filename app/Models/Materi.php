<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Materi extends Model
{
    protected $fillable = [
        'url_listening',
        'url_structure',
        'url_reading',
        'url_pretest',
        'url_posttest'
    ];
}
