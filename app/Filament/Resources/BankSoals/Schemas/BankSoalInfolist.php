<?php

namespace App\Filament\Resources\BankSoals\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;

class BankSoalInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make("Detail Bank Soal")
                    ->icon(Heroicon::OutlinedDocumentText)
                    ->schema([
                        TextEntry::make('jenis')
                            ->badge(),
                        TextEntry::make('nama')
                            ->columnSpan(2),
                        TextEntry::make('jumlah_soal')
                            ->getStateUsing(fn ($record) => $record->soal()->count()),
                        TextEntry::make('gambar')
                            ->label('Gambar')
                            ->color(Color::Blue)
                            ->url(fn ($record) => asset('storage/' . $record->gambar))
                            ->openUrlInNewTab()
                            ->formatStateUsing(fn () => 'Lihat Gambar'),
                        TextEntry::make('audio')
                            ->label('Audio')
                            ->color(Color::Blue)
                            ->url(fn ($record) => asset('storage/' . $record->audio))
                            ->openUrlInNewTab()
                            ->formatStateUsing(fn () => 'Putar Audio'),
                    ])
                    ->columns(6)
            ])
            ->columns(1);
    }
}
