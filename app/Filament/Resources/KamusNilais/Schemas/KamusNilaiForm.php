<?php

namespace App\Filament\Resources\KamusNilais\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class KamusNilaiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make("Formulir Kamus Nilai")
                    ->icon(Heroicon::OutlinedDocumentText)
                    ->schema([
                    TextInput::make('jumlah_benar')
                        ->required()
                        ->numeric(),
                    TextInput::make('listening')
                        ->required()
                        ->numeric(),
                    TextInput::make('reading')
                        ->required()
                        ->numeric(),
                    TextInput::make('structure')
                        ->required()
                        ->numeric(),
                ])
            ]);
    }
}
