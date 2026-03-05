<?php

namespace App\Filament\Resources\Batches\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BatchInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Jadwal')
                    ->columnSpanFull()
                    ->columns([
                        'default' => 2,
                        'xl' => 4
                    ])
                    ->schema([
                        TextEntry::make('judul')
                            ->columnSpan([
                                'default' => 2,
                                'xl' => 1
                            ]),
                        TextEntry::make('mulai')
                            ->label('Mulai')
                            ->dateTime('d F Y, H:i'),
                        TextEntry::make('tutup')
                            ->label('Selesai')
                            ->dateTime('d F Y, H:i'),
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
                            ->getStateUsing(fn($record) => $record->pesertaBatch?->count()),
                        TextEntry::make('jumlah_jadwal_tes')
                            ->numeric()
                            ->getStateUsing(fn($record) => $record->jadwal?->count())
                    ])
            ]);
    }
}
