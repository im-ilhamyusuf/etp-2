<?php

namespace App\Filament\Resources\Biodatas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BiodatasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('row_index')
                    ->label("No")
                    ->rowIndex()
                    ->width('20px'),
                TextColumn::make('user.name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('jenis_kelamin')
                    ->badge()
                    ->getStateUsing(fn ($record) => $record->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan')
                    ->color(fn ($record) => $record->jenis_kelamin == 'L' ? 'success' : 'warning'),
                TextColumn::make('pendidikan_terakhir')
                    ->badge(),
                TextColumn::make('nim')
                    ->label("NIM")
                    ->searchable(),
                TextColumn::make('nidn')
                    ->label("NIDN")
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
            ]);
    }
}
