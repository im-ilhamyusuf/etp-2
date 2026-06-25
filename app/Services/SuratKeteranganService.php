<?php

namespace App\Services;

use App\Models\Peserta;
use Endroid\QrCode\QrCode as EndroidQrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\ErrorCorrectionLevel;
use Illuminate\Support\Facades\Http;
use PhpOffice\PhpWord\TemplateProcessor;

class SuratKeteranganService
{
    public function generate(Peserta $peserta): string
    {
        $templatePath = storage_path('app/templates/surat_keterangan.docx');

        abort_if(!file_exists($templatePath), 500, 'Template surat keterangan tidak ditemukan.');

        $template = new TemplateProcessor($templatePath);

        // Generate QR Code
        $qrPath = $this->generateQrCode($peserta);

        // Isi placeholder teks
        $template->setValues([
            'nama'          => $peserta->user->name,
            'nim'           => $peserta->nim,
            'tempat_lahir'  => $peserta->tempat_lahir,
            'tanggal_lahir' => \Carbon\Carbon::parse($peserta->tanggal_lahir)
                ->translatedFormat('j F Y'),
            'jurusan'       => $peserta->jurusan,
            'program_studi' => $peserta->program_studi,
            'tanggal_sk'    => \Carbon\Carbon::parse($peserta->tanggal_sk)
                ->translatedFormat('j F Y'),
            'no_sk'         => $peserta->no_sk,
            'tahun'         => \Carbon\Carbon::parse($peserta->tanggal_sk)->year,
        ]);

        // Sisipkan QR Code
        $template->setImageValue('qrcode', [
            'path'   => $qrPath,
            'width'  => 100,
            'height' => 100,
            'ratio'  => false,
        ]);

        // Simpan docx sementara
        $docxPath = storage_path('app/temp/sk_' . $peserta->id . '.docx');
        $template->saveAs($docxPath);

        // Hapus file QR temp
        unlink($qrPath);

        // Konversi ke PDF
        $pdfPath = $this->convertToPdf($docxPath);

        // Hapus file DOCX temp
        unlink($docxPath);

        return $pdfPath;
    }

    private function generateQrCode(Peserta $peserta): string
    {
        $information = [
            ['Nama'          => $peserta->user->name],
            ['NIM'           => $peserta->nim],
            ['Jurusan'       => $peserta->jurusan],
            ['Program Studi' => $peserta->program_studi],
            ['No. SK'        => $peserta->no_sk],
            ['Tanggal SK'    => \Carbon\Carbon::parse($peserta->tanggal_sk)->translatedFormat('j F Y')],
        ];

        $responseDigitalSign = Http::post('http://api-esign.itg.ac.id/api/document', [
            'subject'     => 'Surat Keterangan',
            'information' => $information,
        ]);

        if ($responseDigitalSign->failed()) {
            abort($responseDigitalSign->status(), 'Gagal menghubungi API E-Sign.');
        }

        $urlDigitalSign = $responseDigitalSign->json('data.url');

        abort_if(!$urlDigitalSign, 500, 'URL digital sign tidak ditemukan dalam respons.');

        $qrPath = storage_path('app/temp/qr_' . $peserta->id . '.png');

        if (!is_dir(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0775, true);
        }

        $qrCode = EndroidQrCode::create($urlDigitalSign)
            ->setErrorCorrectionLevel(ErrorCorrectionLevel::High)
            ->setSize(300)
            ->setMargin(10);

        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        $result->saveToFile($qrPath);

        return $qrPath;
    }

    private function convertToPdf(string $docxPath): string
    {
        $outputDir = storage_path('app/temp');
        $pdfPath   = str_replace('.docx', '.pdf', $docxPath);

        $soffice = '"C:\\Program Files\\LibreOffice\\program\\soffice.exe"';
        $command = "{$soffice} --headless --convert-to pdf --outdir \"{$outputDir}\" \"{$docxPath}\"";

        shell_exec($command);

        abort_if(!file_exists($pdfPath), 500, 'Gagal mengkonversi ke PDF.');

        return $pdfPath;
    }

    public function generateLampiran(Peserta $peserta): string
    {
        $templatePath = storage_path('app/templates/lampiran_sk.docx');

        abort_if(!file_exists($templatePath), 500, 'Template lampiran tidak ditemukan.');

        $template = new TemplateProcessor($templatePath);

        // Isi placeholder teks
        $template->setValues([
            'nama'          => $peserta->user->name,
            'nim'           => $peserta->nim,
            'program_studi' => $peserta->program_studi,
        ]);

        // Simpan docx sementara
        $docxPath = storage_path('app/temp/lampiran_sk_' . $peserta->id . '.docx');
        $template->saveAs($docxPath);

        return $docxPath;
    }
}
