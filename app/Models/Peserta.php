<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peserta extends Model
{
    protected $fillable = [
        'user_id',
        'jenis_kelamin',
        'foto',
        'nik',
        'tempat_lahir',
        'tanggal_lahir',
        'agama',
        'pendidikan_terakhir',
        'tahun_lulus',
        'pekerjaan',
        'instansi',
        'nim',
        'nidn',
        'kewarganegaraan',
        'bahasa',
        'no_hp',
        'email',
        'alamat',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if ($model->no_peserta) return;

            $lastNumber = static::lockForUpdate()
                ->where('no_peserta', 'like', 'ETP%')
                ->max('no_peserta');

            $next = $lastNumber ? intval(substr($lastNumber, 3)) + 1 : 1;

            $model->no_peserta = "ETP" . str_pad($next, 4, '0', STR_PAD_LEFT);
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jadwal()
    {
        return $this->hasMany(PesertaJadwal::class);
    }

    protected $appends = ['status'];

    public function getStatusAttribute()
    {
        return $this->nim != null ? 'mahasiswa' : 'non mahasiswa';
    }
}
