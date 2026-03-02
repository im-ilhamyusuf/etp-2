<?php

namespace App\Filament\Resources\BankSoals\Tables;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class BankSoalsTable
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
                    ->width('100px'),
                TextColumn::make('sesi')
                    ->width('70px'),
                TextColumn::make('judul')
                    ->searchable(),
                TextColumn::make('jumlah_soal')
                    ->getStateUsing(fn($record) => $record->soal->count())
                    ->width('150px'),
                TextColumn::make('gambar')
                    ->formatStateUsing(fn() => 'Lihat')
                    ->color('primary')
                    ->icon(Heroicon::Eye)
                    ->width('150px')
                    ->action(
                        Action::make('lihatGambar')
                            ->modalHeading('Gambar Pengantar')
                            ->modalWidth('xl')
                            ->modalContent(fn($record) => new HtmlString(
                                '<img src="' . asset('storage/' . $record->gambar) . '" class="w-full rounded-lg">'
                            ))
                            ->modalFooterActions()
                            ->modalSubmitAction(false)
                            ->modalCancelAction(false)
                    ),
                TextColumn::make('audio')
                    ->label('Audio')
                    ->color(Color::Blue)
                    ->url(fn($record) => $record->audio ? asset('storage/' . $record->audio) : null)
                    ->openUrlInNewTab()
                    ->formatStateUsing(fn() => 'Buka')
                    ->icon(Heroicon::Link)
                    ->width('150px'),
            ])
            ->defaultSort('sesi')
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make()
                        ->visible(fn($record) => $record->soal->count() == 0),
                ])
            ]);
    }
}
