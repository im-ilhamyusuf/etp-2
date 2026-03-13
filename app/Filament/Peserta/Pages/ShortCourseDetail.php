<?php

namespace App\Filament\Peserta\Pages;

use App\Models\Jadwal;
use App\Models\Materi;
use App\Models\PesertaBatch;
use App\Models\PesertaJadwal;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Pages\Page;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class ShortCourseDetail extends Page implements HasInfolists, HasTable
{
    use InteractsWithInfolists, InteractsWithTable;

    protected static ?string $slug = 'short-course/{record}';
    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.peserta.pages.short-course-detail';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    public PesertaBatch $record;

    public function mount(PesertaBatch $record): void
    {
        // Pastikan hanya peserta pemilik yang bisa akses
        abort_unless(
            $record->peserta_id === auth()->user()->peserta->id,
            403
        );

        $this->record = $record->load('batch');
    }

    public function courseInfolist(Schema $schema): Schema
    {
        return $schema
            ->record($this->record)
            ->schema([
                Section::make('Informasi Short Course')
                    ->columns([
                        'default' => 2,
                        'xl' => 4
                    ])
                    ->schema([
                        TextEntry::make('batch.judul')
                            ->label('Judul')
                            ->columnSpan([
                                'default' => 2,
                                'xl' => 1
                            ]),

                        TextEntry::make('batch.mulai')
                            ->label('Mulai')
                            ->dateTime('d F Y, H:i'),

                        TextEntry::make('batch.tutup')
                            ->label('Selesai')
                            ->dateTime('d F Y, H:i'),

                        IconEntry::make('batch.status')
                            ->label('Status Jadwal')
                            ->boolean()
                            ->columnSpan([
                                'default' => 2,
                                'xl' => 1
                            ]),

                        TextEntry::make('pretest')
                            ->label('Pre-test'),

                        TextEntry::make('posttest')
                            ->label('Post-test'),

                        TextEntry::make('catatan')
                            ->state(fn() => 'Selesaikan Pre-test untuk mengakses materi dan post-test')
                            ->columnSpan([
                                'default' => 2,
                                'xl' => 2
                            ]),
                    ]),
            ]);
    }

    public function materiInfolist(Schema $schema): Schema
    {
        $materi = Materi::first();

        return $schema
            ->record($materi)
            ->schema([
                Section::make('Materi Course')
                    ->columns(5)
                    ->schema([
                        TextEntry::make('url_pretest')
                            ->label('Pre-test')
                            ->state('Buka')
                            ->url(fn($record) => $record->url_pretest)
                            ->color(Color::Blue)
                            ->icon(Heroicon::Link)
                            ->openUrlInNewTab(),

                        TextEntry::make('url_listening')
                            ->label('Listening')
                            ->state(fn() => $this->pretest() ? 'Buka' : 'Terkunci')
                            ->url(fn($record) => $this->pretest() ? $record->url_listening : null)
                            ->color(fn() => $this->pretest() ? Color::Blue : Color::Gray)
                            ->icon(fn() => $this->pretest() ? Heroicon::Link : Heroicon::LockClosed)
                            ->openUrlInNewTab(),

                        TextEntry::make('url_structure')
                            ->label('Structure')
                            ->state(fn() => $this->pretest() ? 'Buka' : 'Terkunci')
                            ->url(fn($record) => $this->pretest() ? $record->url_structure : null)
                            ->color(fn() => $this->pretest() ? Color::Blue : Color::Gray)
                            ->icon(fn() => $this->pretest() ? Heroicon::Link : Heroicon::LockClosed)
                            ->openUrlInNewTab(),

                        TextEntry::make('url_reading')
                            ->label('Reading')
                            ->state(fn() => $this->pretest() ? 'Buka' : 'Terkunci')
                            ->url(fn($record) => $this->pretest() ? $record->url_reading : null)
                            ->color(fn() => $this->pretest() ? Color::Blue : Color::Gray)
                            ->icon(fn() => $this->pretest() ? Heroicon::Link : Heroicon::LockClosed)
                            ->openUrlInNewTab(),

                        TextEntry::make('url_posttest')
                            ->label('Post-test')
                            ->state(fn() => $this->pretest() ? 'Buka' : 'Terkunci')
                            ->url(fn($record) => $this->pretest() ? $record->url_posttest : null)
                            ->color(fn() => $this->pretest() ? Color::Blue : Color::Gray)
                            ->icon(fn() => $this->pretest() ? Heroicon::Link : Heroicon::LockClosed)
                            ->openUrlInNewTab(),
                    ])
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Jadwal::query()->where('batch_id', $this->record->batch_id)->latest())
            ->columns([
                TextColumn::make('row_index')
                    ->rowIndex()
                    ->label('#')
                    ->width('50px'),
                TextColumn::make('mulai')
                    ->label("Mulai")
                    ->formatStateUsing(fn($state) => $state->translatedFormat('j F Y H:i')),
                TextColumn::make('tutup')
                    ->label("Selesai")
                    ->formatStateUsing(fn($state) => $state->translatedFormat('j F Y H:i')),
                IconColumn::make('status')
                    ->boolean(),
                TextColumn::make('biaya')
                    ->numeric()
                    ->prefix('Rp')
                    ->getStateUsing(fn($record) => auth()->user()->peserta?->status == 'mahasiswa' ? $record->biaya_1 : $record->biaya_2),
                TextColumn::make('kuota'),
                TextColumn::make('jumlah_peserta')
                    ->getStateUsing(fn($record) => $record->pesertaJadwal?->count()),
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

                            return $this->shortCourse()
                                && $record->status
                                && $kuotaMasihAda
                                && ! $punyaTesAktif
                                && ! $sudahAmbilJadwalIni;
                        }
                    )
                    ->action(function ($data, $record, $livewire) {
                        PesertaJadwal::create([
                            'peserta_id' => auth()->user()->peserta?->id,
                            'jadwal_id' => $record->id,
                            'bukti_bayar' => $this->record->bukti_bayar
                        ]);

                        auth()->user()->peserta->load('pesertaJadwal');

                        Notification::make()
                            ->success()
                            ->title('Jadwal tes berhasil diambil.')
                            ->send();
                    })
                    ->requiresConfirmation()
            ]);
    }

    protected function pretest(): bool
    {
        return $this->record->pretest != 0;
    }

    protected function shortCourse(): bool
    {
        return $this->record->peserta?->short_course != null;
    }
}
