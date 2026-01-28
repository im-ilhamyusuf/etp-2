<?php

namespace App\Filament\Resources\KamusNilais\Tables;

use App\Filament\Imports\KamusNilaiImporter;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ImportAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class KamusNilaisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('jumlah_benar')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('listening')
                    ->numeric(),
                TextColumn::make('structure')
                    ->numeric(),
                TextColumn::make('reading')
                    ->numeric(),
            ])
            ->defaultSort('jumlah_benar')
            ->filters([
                //
            ])
            ->headerActions([
                ImportAction::make()
                    ->importer(KamusNilaiImporter::class)
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([]);
    }
}
