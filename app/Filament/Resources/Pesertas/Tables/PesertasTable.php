<?php

namespace App\Filament\Resources\Pesertas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PesertasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('row_index')
                    ->label("No")
                    ->rowIndex()
                    ->width('20px'),
                TextColumn::make('no_peserta')
                    ->searchable(),
                TextColumn::make('user.name')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn ($state) => ucwords($state)),
                TextColumn::make('status')
                    ->sortable()
                    ->badge()
                    ->color(fn ($record) => $record->status == 'mahasiswa' ? 'success' : 'warning')
                    ->formatStateUsing(fn ($state) => ucwords($state))
                    ->width('130px'),
                TextColumn::make('jenis_kelamin')
                    ->badge()
                    ->getStateUsing(fn ($record) => $record->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan')
                    ->color(fn ($record) => $record->jenis_kelamin == 'L' ? 'success' : 'warning')
                    ->width('120px'),
                ImageColumn::make('foto')
                    ->disk('public'),
                TextColumn::make('nim')
                    ->label("NIM")
                    ->searchable()
                    ->width('130px'),
                TextColumn::make('nidn')
                    ->label("NIDN")
                    ->searchable()
                    ->width('130px'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
