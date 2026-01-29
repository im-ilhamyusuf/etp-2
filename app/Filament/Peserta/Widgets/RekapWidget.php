<?php

namespace App\Filament\Peserta\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RekapWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    public function getColumns(): int | array
    {
        return [
            'default' => 2,
            'xl' => 4
        ];
    }

    protected function getStats(): array
    {
        return [
            Stat::make("Jadwal Aktif", auth()->user()->peserta?->pesertaJadwal()->whereHas('jadwal', function ($query) {
                $query->where('tutup', '>=', now());
            })
                ->with('jadwal')
                ->count()),
            Stat::make("Jumlah Riyawat", auth()->user()->peserta?->pesertaJadwal()->count()),
            Stat::make("Jumlah Sertifikat", auth()->user()->peserta?->pesertaJadwal()->selesai()->count()),
            Stat::make("Nilai Tertinggi", auth()->user()->peserta?->pesertaJadwal()->selesai()->max('nilai_akhir') ?? 0),
        ];
    }
}
