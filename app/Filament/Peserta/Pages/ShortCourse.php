<?php

namespace App\Filament\Peserta\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Illuminate\Support\HtmlString;

class ShortCourse extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationLabel = 'Short Course';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;
    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.peserta.pages.short-course';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                auth()
                    ->user()
                    ->peserta
                    ->pesertaBatch()
                    ->getQuery()
                    ->latest()
            )
            ->columns([
                TextColumn::make('row_index')
                    ->rowIndex()
                    ->label('#')
                    ->width('50px'),

                TextColumn::make('judul')
                    ->label('Judul')
                    ->getStateUsing(fn($record) => $record->batch->judul),

                TextColumn::make('mulai')
                    ->getStateUsing(fn($record) => $record->batch->mulai->translatedFormat('d F Y, H:i')),

                TextColumn::make('selesai')
                    ->getStateUsing(fn($record) => $record->batch->tutup->translatedFormat('d F Y, H:i')),

                IconColumn::make('status_jadwal')
                    ->label('Status Jadwal')
                    ->boolean()
                    ->getStateUsing(fn($record) => $record->batch->status),

                TextColumn::make('bukti_bayar')
                    ->state('Lihat')
                    ->color('primary')
                    ->icon(Heroicon::Eye)
                    ->action(
                        Action::make('lihatBukti')
                            ->modalHeading('Bukti Pembayaran')
                            ->modalWidth('sm')
                            ->modalContent(fn($record) => new HtmlString(
                                '<img src="' . asset('storage/' . $record->bukti_bayar) . '" class="w-full rounded-lg">'
                            ))
                            ->modalFooterActions()
                            ->modalSubmitAction(false)
                            ->modalCancelAction(false)
                    ),

                IconColumn::make('status_validasi')
                    ->label('Status Validasi')
                    ->boolean()
                    ->getStateUsing(fn($record) => $record->validasi !== null),
            ])
            ->recordActions([
                Action::make('buka')
                    ->label('Buka')
                    ->icon(Heroicon::Eye)
                    ->url(fn($record) => url("/peserta/short-course/{$record->id}"))
                    ->visible(fn($record) => $record->validasi != null)
            ])
            ->emptyStateHeading('Belum Ada Short Course')
            ->emptyStateDescription('Silakan daftar batch terlebih dahulu.');
    }
}
