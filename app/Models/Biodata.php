<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Biodata extends Model
{
    protected $fillable = [
        'user_id',
        'jenis_kelamin',
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
