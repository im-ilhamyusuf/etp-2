<?php

namespace App\Filament\Peserta\Pages;

use App\Models\Peserta;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmptyState;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;

class SuratKeterangan extends Page implements HasInfolists
{
    use InteractsWithInfolists;

    protected string $view = 'filament.peserta.pages.surat-keterangan';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?int $navigationSort = 6;

    public ?Peserta $peserta = null;

    public function mount(): void
    {
        $this->peserta = Peserta::where('user_id', Auth::id())->first();
    }

    public function hasSuratKeterangan(): bool
    {
        return $this->peserta && !is_null($this->peserta->tanggal_sk);
    }

    public function suratKeteranganInfolist(Schema $infolist): Schema
    {

        return $infolist
            ->record($this->peserta)
            ->schema(function () {
                if (is_null($this->peserta?->tanggal_sk)) {
                    return [
                        EmptyState::make('Surat Keterangan Belum Tersedia')
                            ->description('Surat keterangan Anda belum diterbitkan oleh admin')
                            ->icon(Heroicon::OutlinedExclamationTriangle)
                            ->iconColor('danger'),
                    ];
                }

                return [
                    Section::make('Surat Keterangan')
                        ->columnSpanFull()
                        ->columns(5)
                        ->schema([
                            TextEntry::make('no_peserta')
                                ->label('No. Peserta'),
                            TextEntry::make('user.name')
                                ->label('Nama Lengkap'),
                            TextEntry::make('jurusan')
                                ->label('Jurusan'),
                            TextEntry::make('program_studi')
                                ->label('Program Studi'),
                            TextEntry::make('tanggal_sk')
                                ->label('Tanggal SK')
                                ->badge()
                                ->color('success')
                                ->formatStateUsing(fn($state) => \Carbon\Carbon::parse($state)->translatedFormat('j F Y')),
                            Actions::make([
                                Action::make('unduh_sk')
                                    ->label('Unduh Surat Keterangan')
                                    ->icon('heroicon-o-arrow-down-tray')
                                    ->color('danger')
                                    ->size('lg')
                                    ->url(fn() => route('sk.download'))
                                    ->openUrlInNewTab(),
                                Action::make('unduh_lampiran')
                                    ->label('Unduh Lampiran')
                                    ->icon('heroicon-o-paper-clip')
                                    ->color('info')
                                    ->size('lg')
                                    ->url(fn() => route('sk.download.lampiran'))
                                    ->openUrlInNewTab(),
                            ])->columnSpanFull(),
                        ]),
                ];
            });
    }
}
