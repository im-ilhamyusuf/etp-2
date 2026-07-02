<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessJawabanSesi;
use App\Models\BankSoal;
use App\Models\Peserta;
use App\Models\PesertaJadwal;
use App\Models\PesertaSoal;
use App\Models\Soal;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use SimpleSoftwareIO\QrCode\Facades\QrCode;


class UjianController extends Controller
{
    public function mulai(Request $request)
    {
        $user = $request->user();
        $now  = now();

        // 1. pastikan user punya peserta
        if (! $user->peserta) {
            return response()->json([
                'success' => false,
                'message' => 'Peserta belum terdaftar',
            ], 403);
        }

        // 2. ambil jadwal ujian aktif & tervalidasi
        $pesertaJadwal = $user->peserta
            ->pesertaJadwal()
            ->whereNotNull('validasi')
            ->whereNull('selesai')
            ->whereHas(
                'jadwal',
                fn($q) =>
                $q->where('mulai', '<=', $now)
                    ->where('tutup', '>=', $now)
            )
            ->first();

        if (! $pesertaJadwal) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada jadwal ujian aktif',
            ], 403);
        }

        /**
         * 🔒 GUARD UTAMA
         * kalau ujian sudah dimulai → STOP TOTAL
         */
        if ($pesertaJadwal->mulai) {
            return response()->json([
                'success'   => true,
                'message'   => 'Ujian sudah dimulai',
                'mulai'     => $pesertaJadwal->mulai,
                'sesi_soal' => $pesertaJadwal->sesi_soal,
            ]);
        }

        // 3. ambil semua soal (sekali saja)
        $soals = Soal::join('bank_soals', 'bank_soals.id', '=', 'soals.bank_soal_id')
            ->orderByRaw("
            FIELD(bank_soals.jenis, 'listening', 'structure', 'reading')
        ")
            ->orderBy('soals.id')
            ->select('soals.*')
            ->get();

        if ($soals->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Soal belum tersedia',
            ], 500);
        }

        // 4. snapshot soal (PASTI BARU, karena belum mulai)
        $peserta = $user->peserta;

        foreach ($soals as $index => $soal) {
            $peserta->pesertaSoal()->create([
                'jadwal_id'    => $pesertaJadwal->jadwal_id,
                'bank_soal_id' => $soal->bank_soal_id,
                'no' => $index + 1,
                'soal_id'      => $soal->id,
            ]);
        }

        // 5. set mulai ujian (sekali saja)
        // Cek dan isi kode & number jika masih null
        // if (is_null($pesertaJadwal->kode) || is_null($pesertaJadwal->number)) {
        //     $prefix = 'ETP/LP2B-ITG/2026/';

        //     // Ambil number tertinggi yang sudah ada, lalu increment
        //     $lastNumber = PesertaJadwal::whereNotNull('number')
        //         ->max('number');

        //     $nextNumber = $lastNumber ? $lastNumber + 1 : 1;

        //     // Format jadi 4 digit, misal: 0001, 0002, dst
        //     $formattedNumber = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        //     $pesertaJadwal->update([
        //         'kode'      => $prefix,
        //         'nomor'    => $nextNumber,
        //         'mulai'     => $now,
        //         'sesi_soal' => 1,
        //     ]);
        // } else {
        //     $pesertaJadwal->update([
        //         'mulai'     => $now,
        //         'sesi_soal' => 1,
        //     ]);
        // }

        $pesertaJadwal->update([
            'kode' => 'ETP/LP2B-ITG/2026/',
            'mulai'     => $now,
            'sesi_soal' => 1,
        ]);

        return response()->json([
            'success'     => true,
            'message'     => 'Ujian berhasil dimulai',
            'mulai'       => $pesertaJadwal->mulai,
            'total_soal'  => $soals->count(),
            'sesi_soal'   => $pesertaJadwal->sesi_soal,
        ]);
    }

    public function soal(Request $request)
    {
        $user = $request->user();
        $peserta = $user->peserta;

        if (! $peserta) {
            return response()->json([
                'success' => false,
                'message' => 'Peserta belum terdaftar',
            ], 403);
        }

        // 1. ambil jadwal aktif
        $pesertaJadwal = $peserta->pesertaJadwal()
            ->whereNotNull('validasi')
            ->whereNotNull('mulai')
            ->whereNull('selesai')
            ->first();

        if (! $pesertaJadwal) {
            return response()->json([
                'success' => false,
                'message' => 'Ujian belum dimulai',
            ], 403);
        }

        $sesi = $pesertaJadwal->sesi_soal;

        // 2. ambil soal peserta berdasarkan sesi bank soal
        $pesertaSoals = $peserta->pesertaSoal()
            ->with([
                'bankSoal',
                'soal.soalJawaban'
            ])
            ->whereHas(
                'bankSoal',
                fn($q) =>
                $q->where('sesi', $sesi)
            )
            ->where('jadwal_id', $pesertaJadwal->jadwal_id)
            ->orderBy('id')
            ->get();

        if ($pesertaSoals->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Soal sesi ini tidak tersedia',
            ], 404);
        }

        $bankSoal = $pesertaSoals->first()->bankSoal;

        // 3. response
        return response()->json([
            'success' => true,
            'sesi' => $sesi,
            'bank_soal' => [
                'id'     => $bankSoal->id,
                'judul'   => $bankSoal->judul,
                'jenis'  => $bankSoal->jenis,
                'gambar' => $bankSoal->gambar
                    ? asset('storage/' . $bankSoal->gambar)
                    : null,
                'audio'  => $bankSoal->audio
                    ? asset('storage/' . $bankSoal->audio)
                    : null,
            ],
            'batas_sesi' => $pesertaJadwal->batas_sesi,
            'total_soal' => $pesertaSoals->count(),
            'soals' => $pesertaSoals->map(function ($ps) {
                return [
                    'peserta_soal_id' => $ps->id,
                    'soal_id' => $ps->soal->id,
                    'no' => $ps->no,
                    'soal_jawaban_id' => $ps->soal_jawaban_id,
                    'soal' => $ps->soal->soal,
                    'sudah' => $ps->sudah,

                    'gambar' => $ps->soal->gambar
                        ? asset('storage/' . $ps->soal->gambar)
                        : null,

                    'audio' => $ps->soal->audio
                        ? asset('storage/' . $ps->soal->audio)
                        : null,

                    'daftar_jawaban' => ($ps->soal->soalJawaban ?? collect())
                        ->shuffle()
                        ->map(function ($jawaban) {
                            return [
                                'id' => $jawaban->id,
                                'jawaban' => $jawaban->jawaban,
                            ];
                        }),
                ];
            }),
        ]);
    }

    public function submitJawaban(Request $request)
    {
        $request->validate([
            'sesi' => 'required|integer',
            'peserta_soal_id' => 'required|integer',
            'soal_jawaban_id' => 'nullable|integer',
        ]);

        $user = $request->user();
        $peserta = $user->peserta;
        $selesai = false;

        if (! $peserta) {
            return response()->json([
                'success' => false,
                'message' => 'Peserta tidak valid'
            ], 403);
        }

        $pesertaJadwal = $peserta->pesertaJadwal()
            ->whereNotNull('mulai')
            ->whereNull('selesai')
            ->first();

        if (! $pesertaJadwal) {
            return response()->json([
                'success' => false,
                'message' => 'Ujian tidak aktif'
            ], 403);
        }

        // 🔒 pastikan sesi request = sesi aktif
        if ($request->sesi != $pesertaJadwal->sesi_soal) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi tidak valid / sudah berpindah'
            ], 409);
        }

        $bankSoalJenis = PesertaSoal::query()
            ->join('bank_soals', 'bank_soals.id', '=', 'peserta_soals.bank_soal_id')
            ->where('peserta_soals.id', $request->peserta_soal_id)
            ->where('peserta_soals.peserta_id', $peserta->id)
            ->value('bank_soals.jenis');

        $isListening = $bankSoalJenis === 'listening';

        if ($isListening) {
            DB::transaction(function () use ($pesertaJadwal, $peserta, $request, &$selesai) {
                $currentSesi = (int) $pesertaJadwal->sesi_soal;

                $pesertaSoal = PesertaSoal::query()
                    ->join('bank_soals', 'bank_soals.id', '=', 'peserta_soals.bank_soal_id')
                    ->where('peserta_soals.id', $request->peserta_soal_id)
                    ->where('peserta_soals.peserta_id', $peserta->id)
                    ->where('bank_soals.sesi', $currentSesi)
                    ->select('peserta_soals.no', 'peserta_soals.bank_soal_id')
                    ->first();

                if (! $pesertaSoal) {
                    return;
                }

                $lastNoInSession = PesertaSoal::query()
                    ->join('bank_soals', 'bank_soals.id', '=', 'peserta_soals.bank_soal_id')
                    ->where('peserta_soals.peserta_id', $peserta->id)
                    ->where('bank_soals.sesi', $currentSesi)
                    ->max('peserta_soals.no');

                if ((int) $pesertaSoal->no !== (int) $lastNoInSession) {
                    return;
                }

                $nextSesi = $currentSesi + 1;
                $nextSesiExists = BankSoal::where('sesi', $nextSesi)->exists();

                if ($nextSesiExists) {
                    $update = ['sesi_soal' => $nextSesi];

                    if ($nextSesi === 4) {
                        $update['batas_sesi'] = now()->addMinutes(25);
                    }

                    if ($nextSesi === 5) {
                        $update['batas_sesi'] = now()->addMinutes(55);
                    }

                    $pesertaJadwal->update($update);
                } else {
                    $pesertaJadwal->update(['selesai' => now()]);
                    $selesai = true;
                }
            });
        }

        // 🚀 lempar proses BERAT ke job
        ProcessJawabanSesi::dispatch(
            pesertaId: $peserta->id,
            jadwalId: $pesertaJadwal->jadwal_id,
            pesertaSoalId: $request->peserta_soal_id,
            soalJawabanId: $request->soal_jawaban_id
        );

        // ✅ Fresh sekali, pakai terus — aman untuk concurrent users
        $freshJadwal = $pesertaJadwal->fresh();
        $sesiBeerpindah = $request->sesi != $freshJadwal->sesi_soal;

        $nextSesiData = null;

        if (! $selesai && $sesiBeerpindah) {
            $nextSesi = $freshJadwal->sesi_soal;

            $pesertaSoals = $peserta->pesertaSoal()
                ->with(['bankSoal', 'soal.soalJawaban'])
                ->whereHas('bankSoal', fn($q) => $q->where('sesi', $nextSesi))
                ->where('jadwal_id', $freshJadwal->jadwal_id)
                ->orderBy('id')
                ->get();

            $bankSoal = $pesertaSoals->first()?->bankSoal;

            if ($bankSoal) {
                $nextSesiData = [
                    'sesi'       => $nextSesi,
                    'batas_sesi' => $freshJadwal->batas_sesi,
                    'bank_soal'  => [
                        'id'     => $bankSoal->id,
                        'judul'  => $bankSoal->judul,
                        'jenis'  => $bankSoal->jenis,
                        'gambar' => $bankSoal->gambar ? asset('storage/' . $bankSoal->gambar) : null,
                        'audio'  => $bankSoal->audio ? asset('storage/' . $bankSoal->audio) : null,
                    ],
                    'total_soal' => $pesertaSoals->count(),
                    'soals'      => $pesertaSoals->map(function ($ps) {
                        return [
                            'peserta_soal_id' => $ps->id,
                            'soal_id'         => $ps->soal->id,
                            'no'              => $ps->no,
                            'soal_jawaban_id' => $ps->soal_jawaban_id,
                            'soal'            => $ps->soal->soal,
                            'sudah'           => $ps->sudah,
                            'gambar'          => $ps->soal->gambar ? asset('storage/' . $ps->soal->gambar) : null,
                            'audio'           => $ps->soal->audio ? asset('storage/' . $ps->soal->audio) : null,
                            'daftar_jawaban'  => ($ps->soal->soalJawaban ?? collect())
                                ->shuffle()
                                ->map(fn($j) => ['id' => $j->id, 'jawaban' => $j->jawaban]),
                        ];
                    }),
                ];
            }
        }

        return response()->json([
            'success'        => true,
            'message'        => 'Jawaban diterima',
            'selesai'        => $selesai,
            'sesi_berpindah' => $sesiBeerpindah,
            'current_sesi'   => $freshJadwal->sesi_soal,
            'next_sesi_data' => $nextSesiData,
        ]);
    }

    public function gantiSesi(Request $request)
    {
        $request->validate([
            'sesi' => 'required|integer',
        ]);

        $user = $request->user();
        $peserta = $user->peserta;
        $selesai = false;

        if (! $peserta) {
            return response()->json([
                'success' => false,
                'message' => 'Peserta tidak valid',
            ], 403);
        }

        $pesertaJadwal = $peserta->pesertaJadwal()
            ->whereNotNull('mulai')
            ->whereNull('selesai')
            ->first();

        if (! $pesertaJadwal) {
            return response()->json([
                'success' => false,
                'message' => 'Ujian tidak aktif',
            ], 403);
        }

        // 🔒 optimistic lock
        if ((int) $request->sesi !== (int) $pesertaJadwal->sesi_soal) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi tidak valid / sudah berpindah',
            ], 409);
        }

        // 🔍 cek jenis sesi saat ini
        $jenis = BankSoal::where('sesi', $pesertaJadwal->sesi_soal)
            ->value('jenis');

        if ($jenis === 'listening') {
            return response()->json([
                'success' => false,
                'message' => 'Listening tidak bisa ganti sesi manual',
            ], 422);
        }

        DB::transaction(function () use ($pesertaJadwal, &$selesai) {
            $currentSesi = (int) $pesertaJadwal->sesi_soal;
            $nextSesi = $currentSesi + 1;

            $nextSesiExists = BankSoal::where('sesi', $nextSesi)->exists();

            if ($nextSesiExists) {
                $update = [
                    'sesi_soal' => $nextSesi,
                ];

                // ⏱️ set batas waktu per sesi
                if ($nextSesi === 4) {
                    $update['batas_sesi'] = now()->addMinutes(25);
                }

                if ($nextSesi === 5) {
                    $update['batas_sesi'] = now()->addMinutes(55);
                }

                $pesertaJadwal->update($update);
            } else {
                $pesertaJadwal->update([
                    'selesai' => now(),
                ]);

                $selesai = true;
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Berhasil ganti sesi',
            'selesai' => $selesai,
            'current_sesi' => $pesertaJadwal->sesi_soal,
        ]);
    }


    public function sertifikat(Request $request)
    {
        $pesertaJadwalId = $request->peserta_jadwal_id;
        $pesertaJadwal = PesertaJadwal::find($pesertaJadwalId);
        $peserta = $pesertaJadwal->peserta;
        $jadwal = $pesertaJadwal->jadwal;

        Carbon::setLocale('en');

        $levelData = $this->getLevelDanKeterangan((int) $pesertaJadwal->nilai_akhir);

        $data = [
            'nama' => $peserta->user?->name,
            'tempat_lahir' => $peserta->tempat_lahir,
            'tanggal_lahir' => $peserta->tanggal_lahir->translatedFormat('F jS, Y'),
            'nomor_tes' => $pesertaJadwal->kode . $pesertaJadwal->id,
            'tanggal_tes' => $jadwal->mulai->translatedFormat('F jS, Y'),
            'poin_a' => $pesertaJadwal->poin_a,
            'poin_b' => $pesertaJadwal->poin_b,
            'poin_c' => $pesertaJadwal->poin_c,
            'nilai_akhir' => $pesertaJadwal->nilai_akhir,
            'berlaku_sampai' => Carbon::parse($jadwal->mulai)->addYears(2)->format('F jS, Y'),
            'foto' => public_path('storage/' . $peserta->foto),
            'level'      => $levelData['level'],
            'keterangan' => $levelData['keterangan'],
        ];

        $information = [];
        $information[] = ['Name' => $data['nama']];
        $information[] = ['Place & Date of Birth' => $data['tempat_lahir'] . ', ' . $data['tanggal_lahir']];
        $information[] = ['Test Number'                      => $data['nomor_tes']];
        $information[] = ['Listening Comprehension'          => $data['poin_a']];
        $information[] = ['Structure and Written Expression' => $data['poin_b']];
        $information[] = ['Reading Comprehension'            => $data['poin_c']];
        $information[] = ['Total Score'                      => $data['nilai_akhir']];
        $information[] = ['Valid Until'                      => $data['berlaku_sampai']];

        $responseDigitalSign = Http::post('http://api-esign.itg.ac.id/api/document', [
            'subject'     => 'Sertifikat ETP',
            'information' => $information
        ]);

        if ($responseDigitalSign->failed()) {
            return response()->json([
                'error' => 'Gagal menghubungi API E-Sign.',
                'status' => $responseDigitalSign->status(),
                'detail' => $responseDigitalSign->body(),
            ], $responseDigitalSign->status());
        }

        $urlDigitalSign = $responseDigitalSign->json('data.url');

        if (!$urlDigitalSign) {
            return response()->json([
                'error' => 'URL digital sign tidak ditemukan dalam respons.',
                'detail' => $responseDigitalSign->json(),
            ], 500);
        }

        $qrCode = base64_encode(
            QrCode::format('svg')
                ->size(100)
                ->errorCorrection('H')
                ->generate($urlDigitalSign)
        );

        $data['ttd'] = 'data:image/svg+xml;base64,' . $qrCode;

        // Generate PDF
        $pdf = Pdf::loadView('pdf.sertifikat', $data);
        $pdf->setPaper('A5', 'landscape');

        return $pdf->stream('Certificate_ETP_' . $peserta->user?->name . '.pdf');
    }

    function getLevelDanKeterangan(int $nilaiAkhir): array
    {
        return match (true) {
            $nilaiAkhir >= 310 && $nilaiAkhir <= 420 => ['level' => 'A2', 'keterangan' => 'Basic'],
            $nilaiAkhir >= 421 && $nilaiAkhir <= 480 => ['level' => 'B1', 'keterangan' => 'Intermediate'],
            $nilaiAkhir >= 481 && $nilaiAkhir <= 520 => ['level' => 'B2', 'keterangan' => 'Upper Intermediate'],
            $nilaiAkhir >= 521 && $nilaiAkhir <= 600 => ['level' => 'C1', 'keterangan' => 'Advanced'],
            $nilaiAkhir >= 601 && $nilaiAkhir <= 677 => ['level' => 'C2', 'keterangan' => 'Proficient'],
            default                                   => ['level' => null, 'keterangan' => null],
        };
    }
}
