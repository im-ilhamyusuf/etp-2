<?php

namespace App\Filament\Resources\Pesertas\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
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
                    ->label('#')
                    ->rowIndex()
                    ->width('20px'),
                TextColumn::make('no_peserta')
                    ->label('No. Peserta')
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('Nama Lengkap')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn($state) => ucwords($state)),
                IconColumn::make('status_short_course')
                    ->label('Short Course')
                    ->boolean()
                    ->width('130px'),
                TextColumn::make('jenis_kelamin')
                    ->label('Jenis Kelamin')
                    ->badge()
                    ->getStateUsing(fn($record) => $record->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan')
                    ->color(fn($record) => $record->jenis_kelamin == 'L' ? 'success' : 'warning')
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
                ActionGroup::make([
                    EditAction::make(),
                ])
            ]);
    }
}
