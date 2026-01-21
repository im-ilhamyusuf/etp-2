<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make("Formulir User")
                    ->icon(Heroicon::OutlinedDocumentText)
                    ->schema([
                        Group::make([
                            TextInput::make('name')
                                ->label("Nama")
                                ->required(),
                            TextInput::make('email')
                                ->label('Email')
                                ->email()
                                ->required(),
                            Select::make('role')
                                ->label("Peran")
                                ->searchable()
                                ->options([
                                    "admin" => "admin",
                                    "user" => "user"
                                ])
                                ->required(),
                            TextInput::make('password')
                                ->password()
                                ->required(fn ($context) => $context === 'create')
                                ->dehydrated(fn ($state) => filled($state)),
                        ]),
                    ])
            ]);
    }
}
