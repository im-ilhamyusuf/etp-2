<?php

namespace App\Filament\Resources\Materis\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MaterisTable
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
                        if ($record->jenis == 'listening') {
                            return Color::Red;
                        } else if ($record->jenis == 'structure') {
                            return Color::Yellow;
                        } else {
                            return Color::Green;
                        }
                    })
                    ->width('150px'),
                TextColumn::make('judul'),
                TextColumn::make('url_video')
                    ->label('URL Video')
                    ->url(fn($state) => $state)
                    ->openUrlInNewTab()
                    ->formatStateUsing(fn() => 'Buka')
                    ->color(Color::Blue)
                    ->width('150px'),
                TextColumn::make('url_ujian')
                    ->label('URL Ujian')
                    ->url(fn($state) => $state)
                    ->openUrlInNewTab()
                    ->formatStateUsing(fn() => 'Buka')
                    ->color(Color::Blue)
                    ->width('150px'),
            ])
            ->defaultSort('jenis')
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make()
                ])
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
