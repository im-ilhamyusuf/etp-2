<?php

namespace App\Http\Controllers;

use App\Models\Peserta;
use App\Services\SuratKeteranganService;
use Illuminate\Support\Facades\Auth;

class SuratKeteranganController extends Controller
{
    public function download(SuratKeteranganService $service)
    {
        $peserta = Peserta::where('user_id', Auth::id())->firstOrFail();

        abort_if(is_null($peserta->tanggal_sk), 403, 'Surat keterangan belum tersedia.');

        $pdfPath = $service->generate($peserta);

        return response()->download(
            $pdfPath,
            'Surat_Keterangan_' . $peserta->nim . '.pdf'
        )->deleteFileAfterSend(true);
    }

    public function downloadLampiran(SuratKeteranganService $service)
    {
        $peserta = Peserta::where('user_id', Auth::id())->firstOrFail();

        abort_if(is_null($peserta->tanggal_sk), 403, 'Surat keterangan belum tersedia.');

        $docxPath = $service->generateLampiran($peserta);

        return response()->download(
            $docxPath,
            'Lampiran_SK_' . $peserta->nim . '.docx'
        )->deleteFileAfterSend(true);
    }
}
