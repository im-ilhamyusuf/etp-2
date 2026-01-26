<?php

namespace App\Filament\Peserta\Widgets;

use Filament\Notifications\Notification;
use Filament\Schemas\Components\EmptyState;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class LengkapiProfil extends Widget
{
    protected static ?int $sort = 1;
    protected int|string|array $columnSpan = 'full';
    protected string $view = 'filament.peserta.widgets.lengkapi-profil';

    public static function canView(): bool
    {
        return auth()->check() && !auth()->user()->profilLengkap();
    }
}
