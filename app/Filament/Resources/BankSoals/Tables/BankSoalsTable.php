<?php

namespace App\Filament\Resources\BankSoals\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BankSoalsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('row_index')
                    ->label("No")
                    ->rowIndex()
                    ->width('20px'),
                TextColumn::make('jenis')
                    ->badge()
                    ->color(function ($record) {
                        if( $record->jenis == 'listening' ) {
                            return Color::Red;
                        } else if( $record->jenis == 'reading' ) {
                            return Color::Yellow;
                        } else {
                            return Color::Green;
                        }
                    })
                    ->width('150px'),
                TextColumn::make('nama')
                    ->searchable(),
                TextColumn::make('jumlah_soal')
                    ->getStateUsing(fn ($record) => $record->soal->count())
                    ->width('150px'),
                TextColumn::make('gambar')
                    ->label('Gambar')
                    ->color(Color::Blue)
                    ->url(fn ($record) => $record->gambar ? asset('storage/' . $record->gambar) : null)
                    ->openUrlInNewTab()
                    ->formatStateUsing(fn () => 'Lihat Gambar')
                    ->width('150px'),
                TextColumn::make('audio')
                    ->label('Audio')
                    ->color(Color::Blue)
                    ->url(fn ($record) => $record->audio ? asset('storage/' . $record->audio) : null)
                    ->openUrlInNewTab()
                    ->formatStateUsing(fn () => 'Putar Audio')
                    ->width('150px'),
            ])
            ->defaultSort('jenis')
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
