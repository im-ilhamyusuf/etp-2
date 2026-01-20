<?php

namespace App\Filament\Resources\KamusNilais\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class KamusNilaisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('jumlah_benar')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('listening')
                    ->numeric(),
                TextColumn::make('reading')
                    ->numeric(),
                TextColumn::make('structure')
                    ->numeric(),
            ])
            ->defaultSort('jumlah_benar')
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
