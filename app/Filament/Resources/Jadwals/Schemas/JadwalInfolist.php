<?php

namespace App\Filament\Resources\Jadwals\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class JadwalInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Jadwal')
                    ->columnSpanFull()
                    ->columns(4)
                    ->schema([
                        TextEntry::make('mulai')
                            ->formatStateUsing(fn($state) => $state->translatedFormat('j F Y H:i')),
                        TextEntry::make('tutup')
                            ->label('Selesai')
                            ->formatStateUsing(fn($state) => $state->translatedFormat('j F Y H:i')),
                        IconEntry::make('status')
                            ->boolean(),
                        TextEntry::make('batch.judul')
                            ->label('Batch')
                            ->placeholder('-'),
                        TextEntry::make('biaya_1')
                            ->numeric()
                            ->prefix('Rp'),
                        TextEntry::make('biaya_2')
                            ->numeric()
                            ->prefix('Rp'),
                        TextEntry::make('kuota')
                            ->numeric(),
                        TextEntry::make('jumlah_peserta')
                            ->numeric()
                            ->getStateUsing(fn($record) => $record->pesertaJadwal?->count()),
                    ])
            ]);
    }
}
