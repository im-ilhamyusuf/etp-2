<?php

namespace App\Filament\Resources\Materis\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MateriForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Formulir Materi')
                    ->schema([
                        TextInput::make('url_listening')
                            ->label('URL Listening')
                            ->url()
                            ->required(),
                        TextInput::make('url_structure')
                            ->label('URL Structure')
                            ->url()
                            ->required(),
                        TextInput::make('url_reading')
                            ->label('URL Reading')
                            ->url()
                            ->required(),
                        TextInput::make('url_pretest')
                            ->label('URL Pretest')
                            ->url()
                            ->required(),
                        TextInput::make('url_posttest')
                            ->label('URL Posttest')
                            ->url()
                            ->required(),
                    ])
            ]);
    }
}
