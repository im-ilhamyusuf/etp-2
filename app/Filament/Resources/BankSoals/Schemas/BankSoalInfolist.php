<?php

namespace App\Filament\Resources\BankSoals\Schemas;

use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\HtmlString;

class BankSoalInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make("Detail Bank Soal")
                    ->columns([
                        'default' => 1,
                        'md' => 3,
                    ])
                    ->schema([
                        TextEntry::make('jenis')
                            ->badge(),
                        TextEntry::make('sesi'),
                        TextEntry::make('judul'),
                        TextEntry::make('jumlah_soal')
                            ->getStateUsing(fn($record) => $record->soal()->count()),
                        TextEntry::make('gambar')
                            ->formatStateUsing(fn() => 'Lihat')
                            ->color('primary')
                            ->icon(Heroicon::Eye)
                            ->action(
                                Action::make('lihatGambar')
                                    ->modalHeading('Gambar Pengantar')
                                    ->modalWidth('xl')
                                    ->modalContent(fn($record) => new HtmlString(
                                        '<img src="' . asset('storage/' . $record->gambar) . '" class="w-full rounded-lg">'
                                    ))
                                    ->modalFooterActions()
                                    ->modalSubmitAction(false)
                                    ->modalCancelAction(false)
                            ),
                        TextEntry::make('audio')
                            ->label('Audio')
                            ->color(Color::Blue)
                            ->url(fn($record) => asset('storage/' . $record->audio))
                            ->openUrlInNewTab()
                            ->formatStateUsing(fn() => 'Buka')
                            ->icon(Heroicon::Link),
                    ])
            ])
            ->columns(1);
    }
}
