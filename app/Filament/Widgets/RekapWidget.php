<?php

namespace App\Filament\Widgets;

use App\Models\Jadwal;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RekapWidget extends StatsOverviewWidget
{
    public function getColumns(): int | array
    {
        return 2;
    }

    protected function getStats(): array
    {
        return [
            Stat::make("Jadwal Aktif", Jadwal::aktif()->count()),
            Stat::make("Total Jadwal", Jadwal::count()),
        ];
    }
}
