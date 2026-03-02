<?php

namespace App\Filament\Resources\Materis\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MaterisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('row_index')
                    ->label("#")
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
                    ->url(fn($record) => $record->url_video)
                    ->openUrlInNewTab()
                    ->formatStateUsing(fn() => 'Buka')
                    ->icon(Heroicon::Link)
                    ->color(Color::Blue)
                    ->width('150px'),
                TextColumn::make('url_ujian_1')
                    ->label('URL Ujian 1')
                    ->url(fn($record) => $record->url_ujian)
                    ->openUrlInNewTab()
                    ->formatStateUsing(fn() => 'Buka')
                    ->icon(Heroicon::Link)
                    ->color(Color::Blue)
                    ->width('150px'),
                TextColumn::make('url_ujian_2')
                    ->label('URL Ujian 2')
                    ->url(fn($record) => $record->url_ujian)
                    ->openUrlInNewTab()
                    ->formatStateUsing(fn() => 'Buka')
                    ->icon(Heroicon::Link)
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
