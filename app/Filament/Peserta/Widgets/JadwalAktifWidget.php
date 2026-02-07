<?php

namespace App\Filament\Peserta\Widgets;

use App\Models\Jadwal;
use App\Models\PesertaJadwal;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class JadwalAktifWidget extends TableWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn(): Builder => Jadwal::aktif())
            ->columns([
                TextColumn::make('mulai')
                    ->label("Jadwal Mulai")
                    ->formatStateUsing(fn($state) => $state->translatedFormat('j F Y H:i')),
                TextColumn::make('tutup')
                    ->label("Jadwal Tutup")
                    ->formatStateUsing(fn($state) => $state->translatedFormat('j F Y H:i')),
                TextColumn::make('biaya')
                    ->numeric()
                    ->prefix('Rp')
                    ->getStateUsing(fn($record) => auth()->user()->peserta?->status == 'mahasiswa' ? $record->biaya_1 : $record->biaya_2),
                TextColumn::make('kuota'),
                TextColumn::make('jumlah_peserta')
                    ->getStateUsing(fn($record) => $record->pesertaJadwal?->count()),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                Action::make("ambil_tes")
                    ->label('Ambil Tes')
                    ->icon(Heroicon::ChevronDoubleRight)
                    ->visible(
                        function ($record) {
                            // 1️⃣ profil wajib lengkap
                            if (! auth()->user()->profilLengkap()) {
                                return false;
                            }

                            $peserta = auth()->user()->peserta;

                            // 2️⃣ kuota masih tersedia
                            $jumlahPeserta = PesertaJadwal::where('jadwal_id', $record->id)->count();
                            $kuotaMasihAda = $jumlahPeserta < $record->kuota;


                            // 3️⃣ masih punya tes aktif (jadwal lain)
                            $punyaTesAktif = PesertaJadwal::where('peserta_id', $peserta->id)
                                ->whereHas('jadwal', fn($q) => $q->where('tutup', '>', now()))
                                ->whereNull('selesai')
                                ->exists();

                            // 4️⃣ sudah pernah ambil jadwal INI (walau sudah selesai)
                            $sudahAmbilJadwalIni = PesertaJadwal::where('peserta_id', $peserta->id)
                                ->where('jadwal_id', $record->id)
                                ->exists();

                            return $kuotaMasihAda
                                && ! $punyaTesAktif
                                && ! $sudahAmbilJadwalIni;
                        }
                    )
                    ->schema([
                        FileUpload::make('bukti_bayar')
                            ->aboveContent("Silakan unggah bukti pembayaran untuk booking jadwal tes.")
                            ->image()
                            ->disk('public')
                            ->directory('peserta_jadwal')
                            ->required()
                    ])
                    ->action(function ($data, $record, $livewire) {
                        PesertaJadwal::create([
                            'peserta_id' => auth()->user()->peserta?->id,
                            'jadwal_id' => $record->id,
                            'bukti_bayar' => $data['bukti_bayar']
                        ]);

                        auth()->user()->peserta->load('pesertaJadwal');

                        Notification::make()
                            ->success()
                            ->title('Berhasil')
                            ->body('Bukti pembayaran berhasil diunggah dan jadwal tes telah dibuat.')
                            ->send();
                    })
                    ->modalWidth(Width::Medium)
            ])
            ->toolbarActions([]);
    }
}
