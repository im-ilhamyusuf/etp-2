<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    protected $fillable = [
        'mulai',
        'tutup',
        'kuota',
        'biaya_1',
        'biaya_2'
    ];

    protected $appends = ['status'];

    public function getStatusAttribute()
    {
        return $this->tutup >= now();
    }

    public function scopeAktif($query)
    {
        return $query->where('tutup', '>=', now());
    }
}
