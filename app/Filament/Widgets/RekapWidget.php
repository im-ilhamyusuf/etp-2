<?php

namespace App\Filament\Widgets;

use App\Models\Jadwal;
use App\Models\Peserta;
use App\Models\PesertaJadwal;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RekapWidget extends StatsOverviewWidget
{
    public function getColumns(): int | array
    {
        return [
            'default' => 2,
            'lg' => 4,
        ];
    }

    protected function getStats(): array
    {
        $now = now();

        return [
            Stat::make("Jumlah Peserta", Peserta::count()),
            Stat::make("Jumlah Jadwal", Jadwal::count()),
            Stat::make("Jadwal Aktif", Jadwal::aktif()->count()),
            Stat::make("Jumlah Sertifikat", PesertaJadwal::selesai()->count()),

            // ✅ Tambahan
            Stat::make(
                "Sedang Ujian",
                PesertaJadwal::whereNotNull('mulai')
                    ->whereNull('selesai')
                    ->count()
            )->color('warning'),

            Stat::make(
                "Menunggu Validasi",
                PesertaJadwal::whereNull('validasi')
                    ->whereNull('selesai')
                    ->count()
            )->color('danger'),

            Stat::make(
                "Peserta Lulus",
                PesertaJadwal::selesai()
                    ->where('nilai_akhir', '>=', 400)
                    ->count()
            )->color('success'),

            Stat::make(
                "Peserta Tidak Lulus",
                PesertaJadwal::selesai()
                    ->where('nilai_akhir', '<', 400)
                    ->whereNotNull('nilai_akhir')
                    ->count()
            )->color('danger'),
        ];
    }
}
