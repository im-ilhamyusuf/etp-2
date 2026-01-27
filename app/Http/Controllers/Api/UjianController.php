<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Soal;
use Illuminate\Http\Request;

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

        // 3. kalau sudah mulai (idempotent)
        if ($pesertaJadwal->mulai) {
            return response()->json([
                'success'   => true,
                'message'   => 'Ujian sudah dimulai',
                'mulai'     => $pesertaJadwal->mulai,
                'sesi_soal' => $pesertaJadwal->sesi_soal,
            ]);
        }

        // 4. ambil semua soal global (urut jenis + urutan)
        $soals = Soal::join('bank_soals', 'bank_soals.id', '=', 'soals.bank_soal_id')
            ->orderByRaw("
                FIELD(bank_soals.jenis, 'listening', 'structure', 'reading')
            ")
            ->orderBy('soals.id') // atau soals.urutan kalau ada
            ->select('soals.*')
            ->get();

        if ($soals->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Soal belum tersedia',
            ], 500);
        }

        // 5. simpan snapshot soal ke peserta_soals
        $peserta = $user->peserta;

        if ($peserta->pesertaSoal()->where('jadwal_id', $pesertaJadwal->jadwal_id)->exists()) {
            return response()->json([
                'success' => true,
                'message' => 'Soal ujian sudah disiapkan',
                'mulai'   => $pesertaJadwal->mulai,
            ]);
        }

        foreach ($soals as $soal) {
            $peserta->pesertaSoal()->create([
                'jadwal_id' => $pesertaJadwal->jadwal_id,
                'bank_soal_id' => $soal->bank_soal_id,
                'soal_id' => $soal->id,
            ]);
        }

        // 6. set mulai ujian
        $pesertaJadwal->update([
            'mulai'     => $now,
            'sesi_soal' => 1,
        ]);

        return response()->json([
            'success'     => true,
            'message'     => 'Ujian berhasil dimulai',
            'mulai'       => $pesertaJadwal->mulai,
            'total_soal'  => $soals->count(),
            'sesi_soal'   => 1,
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
}
