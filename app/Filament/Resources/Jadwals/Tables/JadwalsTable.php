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
                    ->dateTime(),
                TextColumn::make('tutup')
                    ->dateTime(),
                TextColumn::make('biaya_1')
                    ->numeric()
                    ->prefix('Rp')
                    ->width('150px'),
                TextColumn::make('biaya_2')
                    ->numeric()
                    ->prefix('Rp')
                    ->width('150px'),
                IconColumn::make('status')
                    ->boolean()
                    ->width('100px'),
                TextColumn::make('kuota')
                    ->numeric()
                    ->width('100px'),
                TextColumn::make('jumlah_peserta')
                    ->numeric()
                    ->width('150px')
                    ->getStateUsing(fn($record) => $record->peserta?->count()),
            ])
            ->modifyQueryUsing(
                fn($query) =>
                $query->orderByRaw('tutup > NOW() DESC')
                    ->orderBy('mulai', 'desc')
            )
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ]);
    }
}
