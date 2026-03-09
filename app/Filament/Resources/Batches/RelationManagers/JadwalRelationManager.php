<?php

namespace App\Filament\Resources\Batches\RelationManagers;

use App\Filament\Resources\Batches\Pages\ViewBatch;
use App\Filament\Resources\Jadwals\JadwalResource;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class JadwalRelationManager extends RelationManager
{
    protected static string $relationship = 'jadwal';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return $pageClass === ViewBatch::class;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                DateTimePicker::make('mulai')
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if (! $state) {
                            return;
                        }

                        $set('tutup', Carbon::parse($state)->addHours(2));
                    }),

                DateTimePicker::make('tutup')
                    ->required()
                    ->readOnly(),
                TextInput::make('biaya_1')
                    ->required()
                    ->numeric()
                    ->prefix('Rp'),
                TextInput::make('biaya_2')
                    ->required()
                    ->numeric()
                    ->prefix('Rp'),
                TextInput::make('kuota')
                    ->required()
                    ->numeric(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('mulai')
            ->defaultSort('mulai', 'desc')
            ->columns([
                TextColumn::make('mulai')
                    ->dateTime('d F Y H:i')
                    ->searchable(),
                TextColumn::make('tutup')
                    ->dateTime('d F Y H:i')
                    ->searchable(),
                TextColumn::make('biaya_1')
                    ->numeric()
                    ->prefix('Rp')
                    ->width('150px'),
                TextColumn::make('biaya_2')
                    ->numeric()
                    ->prefix('Rp')
                    ->width('150px'),
                IconColumn::make('status')
                    ->boolean()
                    ->width('100px'),
                TextColumn::make('kuota')
                    ->numeric()
                    ->width('100px'),
                TextColumn::make('jumlah_peserta')
                    ->numeric()
                    ->width('150px')
                    ->getStateUsing(fn($record) => $record->pesertaJadwal?->count()),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('Lihat')
                        ->icon(Heroicon::Eye)
                        ->url(fn($record) => JadwalResource::getUrl('view', ['record' => $record])),
                    EditAction::make(),
                    DeleteAction::make(),
                ])
            ])
            ->toolbarActions([]);
    }
}
