<?php

namespace App\Filament\Peserta\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RekapWidget extends StatsOverviewWidget
{
    public function getColumns(): int | array
    {
        return 3;
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
        ];
    }
}
