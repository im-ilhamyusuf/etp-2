<?php

namespace App\Filament\Pages;

use App\Models\PesertaJadwal;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use UnitEnum;

class Sertifikat extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.pages.sertifikat';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentCheck;

    protected static string|UnitEnum|null $navigationGroup = 'Ujian';

    protected static ?int $navigationSort = 2;

    public function table(Table $table)
    {
        return $table
            ->query(PesertaJadwal::query()->whereNotNull('selesai')->orderByDesc('mulai'))
            ->columns([
                TextColumn::make('peserta.no_peserta')
                    ->label('No. Peserta')
                    ->searchable(),
                TextColumn::make('peserta.user.name')
                    ->label('Nama Lengkap')
                    ->searchable(),
                TextColumn::make('jadwal.mulai')
                    ->label("Jadwal Ujian")
                    ->formatStateUsing(fn($state) => $state->translatedFormat('j F Y H:i')),
                TextColumn::make('nilai_akhir')
                    ->label("Nilai Akhir"),
                TextColumn::make('sertifikat')
                    ->label('Sertifikat')
                    ->state(fn($record) => filled($record->selesai) ? 'Unduh' : '')
                    ->icon(fn($record) => filled($record->selesai) ? Heroicon::ArrowDownTray : null)
                    ->color(Color::Blue)
                    ->url(
                        fn($record) => filled($record->selesai)
                            ? route('ujian-sertifikat', ['peserta_jadwal_id' => $record->id])
                            : null
                    )
                    ->openUrlInNewTab()
            ]);
    }
}
