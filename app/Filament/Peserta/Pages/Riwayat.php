<?php

namespace App\Filament\Peserta\Pages;

use Filament\Pages\Page;
use BackedEnum;
use Filament\Support\Icons\Heroicon;

class Riwayat extends Page
{
    protected string $view = 'filament.peserta.pages.riwayat';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;
    protected static ?int $navigationSort = 4;
}
