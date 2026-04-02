<?php

namespace App\Filament\Peserta\Widgets;

use App\Models\Batch;
use App\Models\PesertaBatch;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class BatchAktifWidget extends TableWidget
{
    protected static ?string $heading = 'Short Course';
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';
    public function table(Table $table): Table
    {
        return $table
            ->query(function (): Builder {
                $query = Batch::aktif();

                if (! auth()->user()->profilLengkap()) {
                    $query->whereRaw('1 = 0');
                }

                return $query;
            })
            ->columns([
                TextColumn::make('row_index')
                    ->rowIndex()
                    ->label('#')
                    ->width('50px'),
                TextColumn::make('judul'),
                TextColumn::make('mulai')
                    ->label("Mulai")
                    ->formatStateUsing(fn($state) => $state->translatedFormat('j F Y H:i')),
                TextColumn::make('tutup')
                    ->label("Selesai")
                    ->formatStateUsing(fn($state) => $state->translatedFormat('j F Y H:i')),
                TextColumn::make('biaya')
                    ->numeric()
                    ->prefix('Rp')
                    ->getStateUsing(fn($record) => auth()->user()->peserta?->status == 'mahasiswa' ? $record->biaya_1 : $record->biaya_2),
                TextColumn::make('jumlah_peserta')
                    ->getStateUsing(fn($record) => $record->pesertaBatch?->count()),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                Action::make("ambil_short_course")
                    ->label('Ambil Short Course')
                    ->icon(Heroicon::ChevronDoubleRight)
                    ->visible(
                        function ($record) {
                            $peserta = auth()->user()->peserta;

                            // sudah pernah ambil jadwal INI (walau sudah selesai)
                            $sudahAmbilJadwalIni = PesertaBatch::where('peserta_id', $peserta->id)
                                ->where('batch_id', $record->id)
                                ->exists();

                            // masih punya short course aktif (batch lain)
                            $punyaShortCourseAktif = PesertaBatch::where('peserta_id', $peserta->id)
                                ->whereHas('batch', fn($q) => $q->where('tutup', '>', now()))
                                ->exists();

                            return !$sudahAmbilJadwalIni
                                && !$punyaShortCourseAktif;
                        }
                    )
                    ->schema([
                        FileUpload::make('bukti_bayar_short_course')
                            ->aboveContent("Silakan unggah bukti pembayaran untuk ambil Short Course.")
                            ->image()
                            ->disk('public')
                            ->directory('peserta')
                            ->required()
                    ])
                    ->action(function ($data, $record) {
                        PesertaBatch::create([
                            'peserta_id' => auth()->user()->peserta?->id,
                            'batch_id' => $record->id,
                            'bukti_bayar' => $data['bukti_bayar_short_course']
                        ]);

                        auth()->user()->peserta->load('pesertaJadwal');

                        Notification::make()
                            ->success()
                            ->title('Berhasil')
                            ->body('Bukti pembayaran berhasil diunggah dan Short Course telah diambil.')
                            ->send();
                    })
                    ->modalWidth(Width::Medium)
            ])
            ->toolbarActions([]);
    }
}
