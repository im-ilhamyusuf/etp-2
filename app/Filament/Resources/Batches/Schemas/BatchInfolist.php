<?php

namespace App\Filament\Resources\Batches\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class BatchInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Jadwal')
                    ->icon(Heroicon::OutlinedCalendar)
                    ->columnSpanFull()
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                        'xl' => 4
                    ])
                    ->schema([
                        TextEntry::make('judul'),
                        TextEntry::make('jadwal')
                            ->getStateUsing(fn($record) => $record->mulai->translatedFormat('d F, H:i') . " s.d " . $record->tutup->translatedFormat('d F, H:i')),
                        IconEntry::make('status')
                            ->boolean(),
                        TextEntry::make('biaya_1')
                            ->numeric()
                            ->prefix('Rp'),
                        TextEntry::make('biaya_2')
                            ->numeric()
                            ->prefix('Rp'),
                        TextEntry::make('jumlah_peserta')
                            ->numeric()
                            ->getStateUsing(fn($record) => $record->pesertaJadwal?->count()),
                        TextEntry::make('jumlah_jadwal_tes')
                    ])
            ]);
    }
}
