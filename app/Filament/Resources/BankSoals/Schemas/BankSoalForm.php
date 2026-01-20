<?php

namespace App\Filament\Resources\BankSoals\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BankSoalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make("Formulir Bank Soal")
                    ->columns(2)
                    ->schema([
                        Select::make('jenis')
                            ->options(['listening' => 'Listening', 'reading' => 'Reading', 'structure' => 'Structure'])
                            ->required()
                            ->searchable(),
                        TextInput::make('nama')
                            ->required(),
                        FileUpload::make('gambar')
                            ->disk('public')
                            ->image()
                            ->imageEditor()
                            ->directory('bank_soal')
                            ->required(),
                        FileUpload::make('audio')
                            ->disk('public')
                            ->directory('bank_soal')
                            ->acceptedFileTypes([
                                'audio/mpeg',
                                'audio/wav',
                                'audio/ogg',
                                'audio/mp3',
                            ]),
                    ])
            ])
            ->columns(1);
    }
}
