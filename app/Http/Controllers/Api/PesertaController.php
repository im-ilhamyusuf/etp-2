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
                'photo' => $peserta->foto
                    ? Storage::disk('public')->url($peserta->foto)
                    : null,

                'name' => $user->name,
                'email' => $user->email,
                'no_peserta'   => $peserta->no_peserta,
                'roles' => $user->role,

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
            ->whereNull('selesai')
            ->whereHas('jadwal', function ($query) use ($now) {
                $query
                    ->where('tutup', '>=', $now);
            })
            ->with('jadwal')
            ->first();

        if (! $pesertaJadwal) {
            return response()->json([
                'aktif' => false,
            ]);
        }

        $now = now();

        $jadwal = $pesertaJadwal->jadwal;

        $bisaDikerjakan = $now->between(
            $jadwal->mulai,
            $jadwal->tutup
        );

        return response()->json([
            'aktif' => true,
            'bisa_dikerjakan' => $bisaDikerjakan,
            'status_ujian' => $bisaDikerjakan ? 'Sudah Dimulai' : 'Belum Dimulai',
            'sudah_dimulai'  => ! is_null($pesertaJadwal->mulai),
            'sesi' => $pesertaJadwal->mulai
                ? $pesertaJadwal->sesi_soal
                : null,
            'jadwal' => [
                'id' => $pesertaJadwal->jadwal->id,
                'mulai' => $pesertaJadwal->jadwal->mulai->translatedFormat('j F Y H:i') . " WIB",
                'tutup' => $pesertaJadwal->jadwal->tutup->translatedFormat('j F Y H:i') . " WIB",
            ],
        ]);
    }
}
