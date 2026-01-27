<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessJawabanSesi;
use App\Models\BankSoal;
use App\Models\Soal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        foreach ($soals as $soal) {
            $peserta->pesertaSoal()->create([
                'jadwal_id'    => $pesertaJadwal->jadwal_id,
                'bank_soal_id' => $soal->bank_soal_id,
                'soal_id'      => $soal->id,
            ]);
        }

        // 5. set mulai ujian (sekali saja)
        $pesertaJadwal->update([
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
                'nama'   => $bankSoal->nama,
                'jenis'  => $bankSoal->jenis,
                'gambar' => $bankSoal->gambar
                    ? asset('storage/' . $bankSoal->gambar)
                    : null,
                'audio'  => $bankSoal->audio
                    ? asset('storage/' . $bankSoal->audio)
                    : null,
            ],
            'total_soal' => $pesertaSoals->count(),
            'soals' => $pesertaSoals->map(function ($ps) {
                return [
                    'peserta_soal_id' => $ps->id,
                    'soal_id' => $ps->soal->id,
                    'soal_jawaban_id' => $ps->soal_jawaban_id,
                    'soal' => $ps->soal->soal,

                    'gambar' => $ps->soal->gambar
                        ? asset('storage/' . $ps->soal->gambar)
                        : null,

                    'audio' => $ps->soal->audio
                        ? asset('storage/' . $ps->soal->audio)
                        : null,

                    'jawaban' => ($ps->soal->soalJawaban ?? collect())
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
            'jawaban' => 'required|array',
            'jawaban.*.peserta_soal_id' => 'required|integer',
            'jawaban.*.soal_jawaban_id' => 'nullable|integer',
        ]);

        $user = $request->user();
        $peserta = $user->peserta;

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

        // ===============================
        // 🔥 OPTIMISTIC SESSION TRANSITION
        // ===============================
        DB::transaction(function () use ($pesertaJadwal) {

            $nextSesi = $pesertaJadwal->sesi_soal + 1;

            $nextSesiExists = BankSoal::where('sesi', $nextSesi)->exists();

            if ($nextSesiExists) {
                $update = [
                    'sesi_soal' => $nextSesi,
                ];

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
            }
        });

        // 🚀 lempar proses BERAT ke job
        ProcessJawabanSesi::dispatch(
            pesertaId: $peserta->id,
            jadwalId: $pesertaJadwal->jadwal_id,
            sesi: $request->sesi,
            jawaban: $request->jawaban
        );

        return response()->json([
            'success' => true,
            'message' => 'Jawaban diterima',
            'next_sesi' => $pesertaJadwal->sesi_soal,
        ]);
    }
}
