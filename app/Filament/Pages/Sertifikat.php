<?php

namespace App\Filament\Pages;

use App\Models\PesertaJadwal;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class Sertifikat extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.pages.sertifikat';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentCheck;

    protected static string|UnitEnum|null $navigationGroup = 'Ujian';

    protected static ?int $navigationSort = 4;

    public function table(Table $table)
    {
        return $table
            ->query(PesertaJadwal::query()->whereNotNull('selesai')->orderByDesc('mulai'))
            ->columns([
                TextColumn::make('no')
                    ->rowIndex()
                    ->label('#')
                    ->width('50px'),
                TextColumn::make('peserta.no_peserta')
                    ->label('No. Peserta')
                    ->searchable(),
                TextColumn::make('peserta.user.name')
                    ->label('Nama Lengkap')
                    ->searchable(),
                TextColumn::make('jadwal.mulai')
                    ->label("Jadwal Ujian")
                    ->dateTime('d F Y H:i')
                    ->timezone('Asia/Jakarta')
                    ->searchable(),
                TextColumn::make('selesai')
                    ->label('Selesai Ujian')
                    ->dateTime('d F Y H:i')
                    ->timezone('Asia/Jakarta'),
                TextColumn::make('nilai_akhir')
                    ->label("Nilai Akhir")
                    ->badge()
                    ->color(fn($state) => $state >= 400 ? Color::Green : Color::Red),
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
            ->filters([
                Filter::make('lulus')
                    ->label('Hanya Lulus (≥ 400)')
                    ->query(fn(Builder $query) => $query->where('nilai_akhir', '>=', 400))
                    ->toggle(),
                Filter::make('jadwal_mulai')
                    ->label('Periode Jadwal')
                    ->form([
                        DatePicker::make('dari')
                            ->label('Dari Tanggal')
                            ->timezone('Asia/Jakarta')
                            ->displayFormat('d/m/Y'),
                        DatePicker::make('sampai')
                            ->label('Sampai Tanggal')
                            ->timezone('Asia/Jakarta')
                            ->displayFormat('d/m/Y'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari'],
                                fn(Builder $query, $date): Builder =>
                                $query->whereHas(
                                    'jadwal',
                                    fn($q) =>
                                    $q->whereDate('mulai', '>=', $date)
                                )
                            )
                            ->when(
                                $data['sampai'],
                                fn(Builder $query, $date): Builder =>
                                $query->whereHas(
                                    'jadwal',
                                    fn($q) =>
                                    $q->whereDate('mulai', '<=', $date)
                                )
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
            ]);
    }
}
