<?php

namespace App\Filament\Resources\Jadwals\Schemas;

use Carbon\Carbon;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class JadwalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make("Formulir Jadwal Ujian")
                ->icon(Heroicon::OutlinedDocumentText)
                ->schema([
                    DateTimePicker::make('mulai')
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set) {
                            if (! $state) {
                                return;
                            }

                            $set('tutup', Carbon::parse($state)->addHours(2));
                        }),

                    DateTimePicker::make('tutup')
                        ->required()
                        ->readOnly(),
                    TextInput::make('kuota')
                        ->required()
                        ->numeric(),
                    TextInput::make('biaya_1')
                        ->required()
                        ->numeric()
                        ->prefix('Rp'),
                    TextInput::make('biaya_2')
                        ->required()
                        ->numeric()
                        ->prefix('Rp'),
                ])
            ]);
    }
}
