<?php

namespace App\Models;

use Carbon\Carbon;
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

    protected $casts = [
        "tutup" => 'datetime'
    ];

    protected $appends = ['status'];

    public function getStatusAttribute()
    {
        return $this->tutup?->isFuture() ?? false;
    }

    public function scopeAktif($query)
    {
        return $query->where('tutup', '>=', now());
    }

    public function peserta()
    {
        return $this->hasMany(PesertaJadwal::class);
    }
}
