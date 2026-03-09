<?php

namespace App\Filament\Resources\Batches\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BatchesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('row_index')
                    ->label('#')
                    ->rowIndex()
                    ->width('20px'),
                TextColumn::make('judul')
                    ->searchable(),
                TextColumn::make('mulai')
                    ->dateTime('d F Y, H:i')
                    ->searchable(),
                TextColumn::make('tutup')
                    ->label('Selesai')
                    ->dateTime('d F Y, H:i')
                    ->searchable(),
                TextColumn::make('biaya_1'),
                TextColumn::make('biaya_2'),
                TextColumn::make('jumlah_peserta')
                    ->label('Jumlah Peserta')
                    ->numeric()
                    ->width('150px')
                    ->getStateUsing(fn($record) => $record->pesertaBatch?->count()),
                TextColumn::make('jumlah_jadwal_tes')
                    ->label('Jumlah Jadwal Tes')
                    ->getStateUsing(fn($record) => $record->jadwal?->count()),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make()
                        ->visible(fn($record) => $record->pesertaBatch?->count() == 0)
                ])
            ])
            ->toolbarActions([]);
    }
}
