<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PesertaBatch extends Model
{
    protected $fillable = [
        'peserta_id',
        'batch_id',
        'bukti_bayar',
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
