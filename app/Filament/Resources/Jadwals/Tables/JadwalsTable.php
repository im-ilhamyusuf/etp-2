<?php

namespace App\Filament\Resources\Jadwals\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;

class JadwalsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('row_index')
                    ->rowIndex()
                    ->label('#')
                    ->width('50px'),
                TextColumn::make('mulai')
                    ->dateTime('d F Y H:i')
                    ->searchable(),
                TextColumn::make('tutup')
                    ->label('Selesai')
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
                IconColumn::make('batch_id')
                    ->label('Batch')
                    ->boolean()
                    ->getStateUsing(fn($record) => !is_null($record->batch_id))
                    ->width('100px'),
                TextColumn::make('kuota')
                    ->numeric()
                    ->width('100px'),
                TextColumn::make('jumlah_peserta')
                    ->numeric()
                    ->width('150px')
                    ->getStateUsing(fn($record) => $record->pesertaJadwal?->count()),
            ])
            ->modifyQueryUsing(
                fn($query) =>
                $query->orderByRaw('tutup > NOW() DESC')
                    ->orderBy('mulai', 'desc')
            )
            ->filters([
                Filter::make('mulai')
                    ->label('Tanggal Jadwal')
                    ->form([
                        DatePicker::make('dari')
                            ->label('Dari Tanggal')
                            ->displayFormat('d/m/Y'),
                        DatePicker::make('sampai')
                            ->label('Sampai Tanggal')
                            ->displayFormat('d/m/Y'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari'],
                                fn(Builder $query, $date) =>
                                $query->whereDate('mulai', '>=', $date)
                            )
                            ->when(
                                $data['sampai'],
                                fn(Builder $query, $date) =>
                                $query->whereDate('mulai', '<=', $date)
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['dari'] ?? null) {
                            $indicators[] = 'Dari: ' . \Carbon\Carbon::parse($data['dari'])->translatedFormat('d F Y');
                        }
                        if ($data['sampai'] ?? null) {
                            $indicators[] = 'Sampai: ' . \Carbon\Carbon::parse($data['sampai'])->translatedFormat('d F Y');
                        }

                        return $indicators;
                    }),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make()
                        ->visible(fn($record) => !$record->pesertaJadwal?->count() > 0)
                ])
            ]);
    }
}
