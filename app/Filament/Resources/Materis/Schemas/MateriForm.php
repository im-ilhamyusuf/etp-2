<?php

namespace App\Filament\Resources\Materis\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class MateriForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Formulir Materi')
                    ->icon(Heroicon::OutlinedDocumentText)
                    ->schema([
                        Select::make('jenis')
                            ->options(['listening' => 'Listening', 'structure' => 'Structure', 'reading' => 'Reading'])
                            ->searchable()
                            ->required(),
                        TextInput::make('judul')
                            ->required(),
                        TextInput::make('url_video')
                            ->label('URL Video')
                            ->url()
                            ->required(),
                        TextInput::make('url_ujian_1')
                            ->label("URL Ujian 1")
                            ->url()
                            ->required(),
                        TextInput::make('url_ujian_2')
                            ->label("URL Ujian 2")
                            ->url()
                            ->required(),
                    ])
            ]);
    }
}
