<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PesertaController extends Controller
{
    public function profil(Request $request)
    {
        $user = $request->user();
        $peserta = $user->peserta;

        if (! $peserta) {
            return response()->json([
                'success' => false,
                'message' => 'Profil peserta tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'foto' => $peserta->foto
                    ? Storage::disk('public')->url($peserta->foto)
                    : null,

                'nama_lengkap' => $user->name,
                'no_peserta'   => $peserta->no_peserta,

                'jenis_kelamin' => $peserta->jenis_kelamin === 'L'
                    ? 'Laki-laki'
                    : 'Perempuan',
            ]
        ]);
    }

    public function jadwalAktif(Request $request)
    {
        $user = $request->user();

        // user belum punya peserta
        if (! $user->peserta) {
            return response()->json([
                'aktif' => false,
                'message' => 'Peserta belum terdaftar',
            ]);
        }

        $now = now();

        $pesertaJadwal = $user->peserta
            ->pesertaJadwal()
            ->whereNotNull('validasi')
            ->whereHas('jadwal', function ($query) use ($now) {
                $query
                    ->where('mulai', '<=', $now)
                    ->where('tutup', '>=', $now);
            })
            ->with('jadwal')
            ->first();

        if (! $pesertaJadwal) {
            return response()->json([
                'aktif' => false,
            ]);
        }

        return response()->json([
            'aktif'          => true,
            'sudah_dimulai'  => ! is_null($pesertaJadwal->mulai),
            'sesi'           => $pesertaJadwal->mulai
                ? $pesertaJadwal->sesi_soal
                : null,
            'jadwal'         => $pesertaJadwal->jadwal->only(
                'id',
                'mulai',
                'tutup'
            ),
        ]);
    }
}
