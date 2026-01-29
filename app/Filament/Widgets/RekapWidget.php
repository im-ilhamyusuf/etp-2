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
            'xl' => 4,
        ];
    }

    protected function getStats(): array
    {
        return [
            Stat::make("Jumlah Peserta", Peserta::count()),
            Stat::make("Jumlah Jadwal", Jadwal::count()),
            Stat::make("Jadwal Aktif", Jadwal::aktif()->count()),
            Stat::make("Jumlah Sertifikat", PesertaJadwal::selesai()->count()),
        ];
    }
}
