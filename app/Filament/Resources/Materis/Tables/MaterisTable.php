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
                TextColumn::make('url_listening')
                    ->label('URL Listening')
                    ->url(fn($record) => $record->url_listening)
                    ->openUrlInNewTab()
                    ->formatStateUsing(fn() => 'Buka')
                    ->icon(Heroicon::Link)
                    ->color(Color::Blue),
                TextColumn::make('url_structure')
                    ->label('URL Structure')
                    ->url(fn($record) => $record->url_structure)
                    ->openUrlInNewTab()
                    ->formatStateUsing(fn() => 'Buka')
                    ->icon(Heroicon::Link)
                    ->color(Color::Blue),
                TextColumn::make('url_reading')
                    ->label('URL Reading')
                    ->url(fn($record) => $record->url_reading)
                    ->openUrlInNewTab()
                    ->formatStateUsing(fn() => 'Buka')
                    ->icon(Heroicon::Link)
                    ->color(Color::Blue),
                TextColumn::make('url_pretest')
                    ->label('URL Pretest')
                    ->url(fn($record) => $record->url_pretest)
                    ->openUrlInNewTab()
                    ->formatStateUsing(fn() => 'Buka')
                    ->icon(Heroicon::Link)
                    ->color(Color::Blue),
                TextColumn::make('url_posttest')
                    ->label('URL Posttest')
                    ->url(fn($record) => $record->url_posttest)
                    ->openUrlInNewTab()
                    ->formatStateUsing(fn() => 'Buka')
                    ->icon(Heroicon::Link)
                    ->color(Color::Blue)
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make()
                ])
            ])
            ->toolbarActions([]);
    }
}
