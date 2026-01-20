<?php

namespace App\Filament\Resources\Jadwals\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class JadwalsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('mulai')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('tutup')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('kuota')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('biaya_1')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('biaya_2')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('status')
                    ->boolean()
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ]);
    }
}
