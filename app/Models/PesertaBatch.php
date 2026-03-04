<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PesertaBatch extends Model
{
    protected $fillable = [
        'peserta_id',
        'batch_id',
        'bukti_bayar',
        'validasi',
        'poin_a1',
        'poin_b1',
        'poin_c1',
        'nilai_akhir1',
        'poin_a2',
        'poin_b2',
        'poin_c2',
        'nilai_akhir2',
    ];

    public function peserta()
    {
        return $this->belongsTo(Peserta::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }
}
