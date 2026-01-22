<?php

namespace App\Filament\Resources\Jadwals\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class JadwalInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Jadwal')
                    ->icon(Heroicon::OutlinedCalendar)
                    ->columnSpanFull()
                    ->columns(4)
                    ->schema([
                        TextEntry::make('mulai')
                            ->dateTime(),
                        TextEntry::make('tutup')
                            ->dateTime(),
                        IconEntry::make('status')
                            ->boolean()
                            ->columnSpan(2),
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
                            ->getStateUsing(fn($record) => $record->peserta?->count()),
                    ])
            ]);
    }
}
