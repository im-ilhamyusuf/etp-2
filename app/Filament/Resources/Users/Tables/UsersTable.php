<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('row_index')
                    ->label("No")
                    ->rowIndex()
                    ->width('20px'),
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->formatStateUsing(fn($state) => ucwords($state)),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('role')
                    ->label('Peran')
                    ->badge()
                    ->color(function ($record) {
                        return $record->role == 'admin' ? 'success' : 'warning';
                    }),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make()
                        ->visible(fn($record) => $record->id != auth()->id()),
                ])
            ]);
    }
}
