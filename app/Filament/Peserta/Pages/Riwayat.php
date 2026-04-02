<?php

namespace App\Filament\Peserta\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Illuminate\Support\HtmlString;

class Riwayat extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.peserta.pages.riwayat';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;
    protected static ?int $navigationSort = 5;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                auth()
                    ->user()
                    ->peserta
                    ->pesertaJadwal()
                    ->getQuery()
                    ->latest()
            )
            ->columns([
                TextColumn::make('jadwal.mulai')
                    ->label('Jadwal Mulai')
                    ->formatStateUsing(
                        fn($state) =>
                        $state?->translatedFormat('j F Y H:i')
                    ),

                TextColumn::make('jadwal.tutup')
                    ->label('Jadwal Tutup')
                    ->formatStateUsing(
                        fn($state) =>
                        $state?->translatedFormat('j F Y H:i')
                    ),

                TextColumn::make('bukti_bayar')
                    ->label('Bukti Pembayaran')
                    ->state('Lihat')
                    ->icon(Heroicon::Eye)
                    ->color(Color::Blue)
                    ->action(
                        Action::make('lihat_bukti')
                            ->modalHeading('Bukti Pembayaran')
                            ->modalWidth('sm')
                            ->modalContent(
                                fn($record) =>
                                new HtmlString(
                                    '<img src="' . asset('storage/' . $record->bukti_bayar) . '" class="w-full rounded-lg">'
                                )
                            )
                            ->modalSubmitAction(false)
                            ->modalCancelAction(false)
                    ),

                TextColumn::make('poin_a')
                    ->label('Listening'),

                TextColumn::make('poin_b')
                    ->label('Reading'),

                TextColumn::make('poin_c')
                    ->label('Structure'),

                TextColumn::make('nilai_akhir')
                    ->label('Nilai Akhir'),

                TextColumn::make('sertifikat')
                    ->label('Sertifikat')
                    ->state(fn($record) => filled($record->selesai) ? 'Unduh' : '')
                    ->icon(fn($record) => filled($record->selesai) ? Heroicon::ArrowDownTray : null)
                    ->color(Color::Blue)
                    ->url(
                        fn($record) => filled($record->selesai)
                            ? route('ujian-sertifikat', ['peserta_jadwal_id' => $record->id])
                            : null
                    )
                    ->openUrlInNewTab()
            ])
            ->emptyStateHeading('Belum Ada Riwayat')
            ->emptyStateDescription('Riwayat tes akan muncul di sini setelah Anda mengikuti tes.');
    }
}
