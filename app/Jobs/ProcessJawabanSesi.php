<?php

namespace App\Jobs;

use App\Models\BankSoal;
use App\Models\PesertaJadwal;
use App\Models\PesertaSoal;
use App\Models\SoalJawaban;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;

class ProcessJawabanSesi implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $pesertaId,
        public int $jadwalId,
        public int $sesi,
        public array $jawaban
    ) {}

    public function handle(): void
    {
        DB::transaction(function () {
            foreach ($this->jawaban as $item) {

                $benar = null;

                if (! empty($item['soal_jawaban_id'])) {
                    $benar = SoalJawaban::where('id', $item['soal_jawaban_id'])
                        ->value('benar');
                }

                PesertaSoal::where('id', $item['peserta_soal_id'])
                    ->where('peserta_id', $this->pesertaId)
                    ->update([
                        'soal_jawaban_id' => $item['soal_jawaban_id'],
                        'benar'           => $benar,
                        'updated_at'      => now(),
                    ]);
            }
        });
    }
}
