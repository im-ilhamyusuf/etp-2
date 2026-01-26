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
        foreach ($soals as $soal) {
            $peserta->pesertaSoal()->create([
                'jadwal_id' => $pesertaJadwal->jadwal->id,
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
}
