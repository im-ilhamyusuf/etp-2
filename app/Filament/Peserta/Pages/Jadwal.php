<?php

namespace App\Filament\Peserta\Pages;

use BackedEnum;
use Carbon\Carbon;
use Dom\Text;
use Faker\Core\Color;
use Filament\Actions\Action;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmptyState;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\HtmlString;

class Jadwal extends Page implements HasInfolists
{
    use InteractsWithInfolists;

    protected string $view = 'filament.peserta.pages.jadwal';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendar;
    protected static ?int $navigationSort = 3;

    public function jadwalInfolist(Schema $schema): Schema
    {
        $user = auth()->user();
        $pesertaJadwal = $user->peserta?->pesertaJadwal()->whereHas('jadwal', function ($query) {
            $query->where('tutup', '>=', now());
        })
            ->with('jadwal')
            ->first();

        if (!$pesertaJadwal) {
            return $schema
                ->schema([
                    EmptyState::make("Tidak Ada Jadwal Aktif")
                        ->description('Saat ini Anda belum terdaftar di jadwal aktif manapun')
                        ->icon(Heroicon::OutlinedExclamationTriangle)
                        ->iconColor('danger')
                ]);
        } else {
            return $schema
                ->record($user)
                ->schema([
                    Section::make("Informasi Peserta & Jadwal")
                        ->icon(Heroicon::OutlinedCalendar)
                        ->schema([
                            Flex::make([
                                ImageEntry::make("foto")
                                    ->hiddenLabel()
                                    ->disk('public')
                                    ->getStateUsing(fn() => $user->peserta?->foto)
                                    ->imageHeight('12rem')
                                    ->grow(false),

                                Grid::make([
                                    'default' => 1,
                                    'md' => 2,
                                    'lg' => 3,
                                ])
                                    ->schema([
                                        TextEntry::make('nama_lengkap')
                                            ->label('Nama Lengkap')
                                            ->getStateUsing(fn() => $user->name),
                                        TextEntry::make('no_peserta')
                                            ->label('No. Peserta')
                                            ->getStateUsing(fn() => $user->peserta?->no_peserta),
                                        TextEntry::make('jenis_kelamin')
                                            ->getStateUsing(fn() => $user->peserta?->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan'),
                                        TextEntry::make('jadwal_mulai')
                                            ->getStateUsing(fn() => $pesertaJadwal?->jadwal->mulai)
                                            ->date('d F Y H:i')
                                            ->placeholder('-'),
                                        TextEntry::make('jadwal_tutup')
                                            ->getStateUsing(fn() => $pesertaJadwal?->jadwal->tutup)
                                            ->date('d F Y H:i'),
                                        TextEntry::make('status_ujian')
                                            ->badge()
                                            ->getStateUsing(function () use ($pesertaJadwal) {
                                                if ($pesertaJadwal?->jadwal->mulai >= now()) {
                                                    return 'Belum dimulai';
                                                } else if ($pesertaJadwal?->jadwal->mulai < now()) {
                                                    return 'Aktif';
                                                } else {
                                                    return 'Selesai';
                                                }
                                            })
                                            ->color(function () use ($pesertaJadwal) {
                                                if ($pesertaJadwal?->jadwal->mulai >= now()) {
                                                    return 'warning';
                                                } else if ($pesertaJadwal?->jadwal->mulai < now()) {
                                                    return 'success';
                                                } else {
                                                    return 'danger';
                                                }
                                            }),
                                        TextEntry::make('bukti_pembayaran')
                                            ->state('Lihat')
                                            ->color('primary')
                                            ->icon(Heroicon::Eye)
                                            ->action(
                                                Action::make('lihatBukti')
                                                    ->modalHeading('Bukti Pembayaran')
                                                    ->modalWidth('sm')
                                                    ->modalContent(fn($record) => new HtmlString(
                                                        '<img src="' . asset('storage/' . $pesertaJadwal?->bukti_bayar) . '" class="w-full rounded-lg">'
                                                    ))
                                                    ->modalFooterActions()
                                                    ->modalSubmitAction(false)
                                                    ->modalCancelAction(false)
                                            ),
                                        TextEntry::make('status_pembayaran')
                                            ->label('Status Pembayaran')
                                            ->getStateUsing(fn() => $pesertaJadwal?->validasi != null ? 'Tervalidasi' : 'Menunggu Validasi')
                                            ->badge()
                                            ->color(fn($state) => $pesertaJadwal?->validasi != null ? 'success' : 'danger'),
                                    ])
                            ])->from('md')
                        ])
                ]);
        }
    }
}
