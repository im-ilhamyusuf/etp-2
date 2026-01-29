<?php

namespace App\Filament\Peserta\Pages;

use App\Models\PesertaJadwal;
use Filament\Pages\Page;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\HtmlString;

class Riwayat extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.peserta.pages.riwayat';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;
    protected static ?int $navigationSort = 4;

    protected function getTableQuery(): Builder|Relation|null
    {
        return auth()
            ->user()
            ->peserta
            ->pesertaJadwal()
            ->getQuery()
            ->latest();
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('jadwal.mulai')
                ->label("Jadwal Mulai")
                ->formatStateUsing(fn($state) => $state->translatedFormat('j F Y H:i')),
            TextColumn::make('jadwal.tutup')
                ->label("Jadwal Tutup")
                ->formatStateUsing(fn($state) => $state->translatedFormat('j F Y H:i')),
            TextColumn::make('jadwal.bukti_bayar')
                ->label("Bukti Pembayaran")
                ->state("Lihat")
                ->icon(Heroicon::Eye)
                ->color(Color::Blue)
                ->action(
                    Action::make('lihat_buktu')
                        ->modalHeading('Bukti Pembayaran')
                        ->modalWidth('sm')
                        ->modalContent(fn($record) => new HtmlString(
                            '<img src="' . asset('storage/' . $record->bukti_bayar) . '" class="w-full rounded-lg">'
                        ))
                        ->modalFooterActions()
                        ->modalSubmitAction(false)
                        ->modalCancelAction(false)
                ),
            TextColumn::make('poin_a')
                ->label("Listening"),
            TextColumn::make('poin_b')
                ->label("Reading"),
            TextColumn::make('poin_c')
                ->label("Structure"),
            TextColumn::make('nilai_akhir'),
        ];
    }
}
