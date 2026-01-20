<?php

namespace App\Filament\Resources\Jadwals\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class JadwalInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('mulai')
                    ->dateTime(),
                TextEntry::make('tutup')
                    ->dateTime(),
                TextEntry::make('kuota')
                    ->numeric(),
                TextEntry::make('biaya_1')
                    ->numeric(),
                TextEntry::make('biaya_2')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
