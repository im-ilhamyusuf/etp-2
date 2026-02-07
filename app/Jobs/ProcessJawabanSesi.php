<?php

namespace App\Jobs;

use App\Models\BankSoal;
use App\Models\KamusNilai;
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
        public int $pesertaSoalId,
        public ?int $soalJawabanId
    ) {}


    public function handle(): void
    {
        DB::transaction(function () {
            // 1. simpan 1 jawaban
            $benar = 0;

            if ($this->soalJawabanId) {
                $benar = (int) SoalJawaban::where('id', $this->soalJawabanId)
                    ->value('benar');
            }

            PesertaSoal::where('id', $this->pesertaSoalId)
                ->where('peserta_id', $this->pesertaId)
                ->update([
                    'soal_jawaban_id' => $this->soalJawabanId,
                    'benar'           => $benar,
                    'updated_at'      => now(),
                ]);

            // 2. hitung akumulasi nilai
            $rekap = PesertaSoal::query()
                ->join('bank_soals', 'bank_soals.id', '=', 'peserta_soals.bank_soal_id')
                ->where('peserta_soals.peserta_id', $this->pesertaId)
                ->where('peserta_soals.jadwal_id', $this->jadwalId)
                ->where('peserta_soals.benar', 1)
                ->selectRaw('bank_soals.jenis, COUNT(*) as total')
                ->groupBy('bank_soals.jenis')
                ->pluck('total', 'jenis');

            $listeningBenar = $rekap['listening'] ?? 0;
            $structureBenar = $rekap['structure'] ?? 0;
            $readingBenar   = $rekap['reading'] ?? 0;

            // 3. bandingkan dengan kamus nilai
            $nilaiListening = KamusNilai::where('jumlah_benar', $listeningBenar)
                ->value('listening') ?? 0;

            $nilaiStructure = KamusNilai::where('jumlah_benar', $structureBenar)
                ->value('structure') ?? 0;

            $nilaiReading = KamusNilai::where('jumlah_benar', $readingBenar)
                ->value('reading') ?? 0;

            // 4. hitung nilai akhir
            $nilaiAkhir = round(
                (($nilaiListening + $nilaiStructure + $nilaiReading) / 3) * 10
            );

            // 5. update database
            PesertaJadwal::where('peserta_id', $this->pesertaId)
                ->where('jadwal_id', $this->jadwalId)
                ->update([
                    'poin_a' => $nilaiListening,
                    'poin_b' => $nilaiStructure,
                    'poin_c' => $nilaiReading,
                    'nilai_akhir' => $nilaiAkhir,
                    'updated_at' => now(),
                ]);
        });
    }
}
