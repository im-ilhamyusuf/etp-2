<?php

namespace App\Filament\Pages;

use App\Models\Peserta;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Filament\Tables\Contracts\HasTable;
use UnitEnum;
use Filament\Pages\Page;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\ActionGroup;

class SuratKeterangan extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.pages.surat-keterangan';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static string|UnitEnum|null $navigationGroup = 'Ujian';

    protected static ?int $navigationSort = 5;

    private function generateNoSk(): string
    {
        $last = Peserta::whereNotNull('no_sk')
            ->orderByDesc('no_sk')
            ->value('no_sk');

        $nextNumber = $last ? ((int) $last) + 1 : 1;

        return str_pad($nextNumber, 3, '0', STR_PAD_LEFT); // "001", "002", dst
    }

    public function table(Table $table)
    {
        return $table
            ->query(Peserta::query()
                ->withCount([
                    'pesertaJadwal as tes_selesai_count' => function ($query) {
                        $query->whereNotNull('selesai');
                    }
                ])
                ->withMax([
                    'pesertaJadwal as nilai_tertinggi' => function ($query) {
                        $query->whereNotNull('selesai');
                    }
                ], 'nilai_akhir')  // nama kolom di tabel peserta_jadwal
                ->having('tes_selesai_count', '>=', 3)
                ->orderByDesc('tes_selesai_count'))
            ->columns([
                TextColumn::make('no')
                    ->rowIndex()
                    ->label('#')
                    ->width('50px'),
                TextColumn::make('no_peserta')
                    ->label('No. Peserta')
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('Nama Lengkap')
                    ->searchable(),
                TextColumn::make('jurusan')
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('program_studi')
                    ->label('Program Studi')
                    ->placeholder('-')
                    ->searchable(),
                TextColumn::make('tes_selesai_count')
                    ->label('Jumlah Tes')
                    ->sortable(),
                TextColumn::make('nilai_tertinggi')
                    ->label('Nilai Tertinggi')
                    ->badge()
                    ->color(fn($state) => $state >= 400 ? Color::Green : Color::Red)
                    ->sortable(),
                TextColumn::make('tanggal_sk')
                    ->label('Tanggal SK')
                    ->date('d M Y')
                    ->placeholder('-')
                    ->sortable(),
            ])
            ->filters([
                Filter::make('sudah_sk')
                    ->label('Sudah Ada SK')
                    ->query(fn(Builder $query) => $query->whereNotNull('tanggal_sk')),

                Filter::make('belum_sk')
                    ->label('Belum Ada SK')
                    ->query(fn(Builder $query) => $query->whereNull('tanggal_sk')),

                SelectFilter::make('jurusan')
                    ->label('Jurusan')
                    ->options(
                        fn() => Peserta::query()
                            ->whereNotNull('jurusan')
                            ->distinct()
                            ->pluck('jurusan', 'jurusan')
                            ->toArray()
                    ),
                SelectFilter::make('program_studi')
                    ->label('Program Studi')
                    ->options(
                        fn() => Peserta::query()
                            ->whereNotNull('program_studi')
                            ->distinct()
                            ->pluck('program_studi', 'program_studi')
                            ->toArray()
                    ),
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('isi_tanggal_sk')
                        ->label('Isi Tanggal SK')
                        ->icon('heroicon-o-calendar')
                        ->modalWidth('md')
                        ->color(Color::Blue)
                        ->modalHeading('Isi Tanggal Surat Keterangan')
                        ->modalSubmitActionLabel('Simpan')
                        ->fillForm(fn($record) => [
                            'tanggal_sk' => $record->tanggal_sk,
                        ])
                        ->form([
                            DatePicker::make('tanggal_sk')
                                ->label('Tanggal Surat Keterangan')
                                ->required()
                        ])
                        ->action(function ($record, array $data) {
                            $noSk = $record->no_sk ?? $this->generateNoSk();

                            $record->update([
                                'tanggal_sk' => $data['tanggal_sk'],
                                'no_sk'      => $noSk,
                            ]);

                            Notification::make()
                                ->title('Berhasil')
                                ->body("Tanggal SK dan No. SK ({$noSk}) berhasil disimpan.")
                                ->success()
                                ->send();
                        }),
                    Action::make('hapus_tanggal_sk')
                        ->label('Hapus SK')
                        ->icon('heroicon-o-trash')
                        ->color(Color::Red)
                        ->visible(fn($record) => !is_null($record->tanggal_sk))
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Tanggal SK?')
                        ->modalDescription('Tindakan ini akan menghapus tanggal surat keterangan peserta ini.')
                        ->action(function ($record) {
                            $record->update([
                                'tanggal_sk' => null,
                                'no_sk'      => null,
                            ]);

                            Notification::make()
                                ->title('Berhasil')
                                ->body('Tanggal SK berhasil dihapus.')
                                ->warning()
                                ->send();
                        }),
                ])
            ]);
    }
}
