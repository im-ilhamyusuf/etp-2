<?php

namespace App\Filament\Resources\Jadwals\RelationManagers;

use App\Filament\Resources\Jadwals\Pages\ViewJadwal;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

class PesertaRelationManager extends RelationManager
{
    protected static string $relationship = 'pesertaJadwal';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return $pageClass === ViewJadwal::class;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('no_peserta')
            ->columns([
                TextColumn::make('no_peserta')
                    ->getStateUsing(fn($record) => $record->peserta?->no_peserta)
                    ->width('120px'),
                ImageColumn::make('foto')
                    ->disk('public')
                    ->getStateUsing(fn($record) => $record->peserta?->foto),
                TextColumn::make('nama')
                    ->getStateUsing(fn($record) => $record->peserta?->user?->name),
                TextColumn::make('status')
                    ->getStateUsing(fn($record) => $record->peserta?->status)
                    ->formatStateUsing(fn($state) => ucwords($state)),
                TextColumn::make('jenis_kelamin')
                    ->getStateUsing(fn($record) => $record->peserta?->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan'),
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
                    ->getStateUsing(fn($record) => $record->validasi != null)
                    ->boolean(),
                TextColumn::make('status_ujian')
                    ->badge()
                    ->color(fn($record) => $record->statusUjianColor),
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
                //
            ])
            ->headerActions([])
            ->recordActions([
                ActionGroup::make([
                    Action::make('validasi')
                        ->icon(Heroicon::CheckBadge)
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($record) {
                            $record->update([
                                'validasi' => now(),
                            ]);

                            Notification::make()
                                ->title('Berhasil')
                                ->body('Pembayaran berhasil divalidasi.')
                                ->success()
                                ->send();
                        }),
                    Action::make('detailUjian')
                        ->icon(Heroicon::NumberedList)
                        ->modalWidth(Width::ExtraLarge)
                        ->mountUsing(function (Schema $form, $record) {
                            $form->fill([
                                'mulai' => $record->mulai,
                                'selesai' => $record->selesai,
                                'sesi_soal' => $record->sesi_soal,
                                'batas_sesi' => $record->batas_sesi,
                                'poin_a' => $record->poin_a,
                                'poin_b' => $record->poin_b,
                                'poin_c' => $record->poin_c,
                                'nilai_akhir' => $record->nilai_akhir,
                            ]);
                        })
                        ->schema([
                            Flex::make([
                                ImageEntry::make('peserta.foto')
                                    ->disk('public')
                                    ->hiddenLabel()
                                    ->grow(false),
                                Group::make([
                                    Group::make([
                                        TextEntry::make('peserta.no_peserta'),
                                        TextEntry::make('peserta.user.name')
                                            ->label("Nama"),
                                    ])->columns(2),
                                    Group::make([
                                        TextEntry::make('peserta.jenis_kelamin')
                                            ->formatStateUsing(fn($state) => match ($state) {
                                                'L' => 'Laki-laki',
                                                'P' => 'Perempuan',
                                                default => '-',
                                            }),
                                        TextEntry::make('peserta.status')
                                            ->formatStateUsing(fn($state) => ucwords($state)),
                                    ])->columns(2)
                                ])
                            ]),

                            Group::make([
                                DateTimePicker::make('mulai')
                                    ->label('Waktu mulai'),
                                DateTimePicker::make('selesai')
                                    ->label('Waktu selesai'),
                                TextInput::make('sesi_soal')
                                    ->numeric(),
                                DateTimePicker::make('batas_sesi'),
                                TextInput::make('poin_a')
                                    ->label("Poin Listening")
                                    ->numeric(),
                                TextInput::make('poin_b')
                                    ->label("Poin Structure")
                                    ->numeric(),
                                TextInput::make('poin_c')
                                    ->label("Poin Reading")
                                    ->numeric(),
                                TextInput::make('nilai_akhir')
                                    ->label("Nilai Akhir")
                                    ->numeric(),
                            ])
                                ->inlineLabel()
                        ])
                        ->modalSubmitActionLabel("Simpan")
                        ->action(function ($record, $data) {
                            $record->update($data);

                            Notification::make()
                                ->title('Berhasil')
                                ->body('Informasi ujian peserta berhasil diperbarui.')
                                ->success()
                                ->send();
                        }),
                    DeleteAction::make(),
                ])
            ])
            ->toolbarActions([]);
    }
}
