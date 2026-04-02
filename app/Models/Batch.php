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

    protected $casts = [
        "mulai" => 'datetime',
        "tutup" => 'datetime'
    ];

    protected $appends = ['status'];

    public function getStatusAttribute()
    {
        return $this->tutup?->isFuture() ?? false;
    }

    public function scopeAktif($query)
    {
        return $query
            ->where('mulai', '<=', now())
            ->where('tutup', '>=', now());
    }

    public function pesertaBatch()
    {
        return $this->hasMany(PesertaBatch::class);
    }

    public function jadwal()
    {
        return $this->hasMany(Jadwal::class);
    }
}
