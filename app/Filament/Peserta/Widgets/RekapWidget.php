<?php

namespace App\Filament\Peserta\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RekapWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    public function getColumns(): int | array
    {
        return 2;
    }

    protected function getStats(): array
    {
        return [
            Stat::make("Jadwal Aktif", auth()->user()->peserta?->pesertaJadwal()->whereHas('jadwal', function ($query) {
                $query->where('tutup', '>=', now());
            })
                ->with('jadwal')
                ->count()),
            Stat::make("Total Riyawat", auth()->user()->peserta?->pesertaJadwal()->count()),
        ];
    }
}
