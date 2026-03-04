<?php

namespace App\Filament\Resources\Batches\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class BatchForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make("Formulir Batch")
                    ->columnSpanFull()
                    ->columns([
                        'default' => 1,
                        'xl' => 2
                    ])
                    ->schema([
                        TextInput::make('judul')
                            ->columnSpanFull()
                            ->required(),
                        DateTimePicker::make('mulai')
                            ->required(),
                        DateTimePicker::make('tutup')
                            ->required(),
                        TextInput::make('biaya_1')
                            ->required()
                            ->numeric()
                            ->prefix('Rp'),
                        TextInput::make('biaya_2')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                    ])
            ]);
    }
}
