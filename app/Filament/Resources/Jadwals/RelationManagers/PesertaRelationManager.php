<?php

namespace App\Filament\Resources\Jadwals\RelationManagers;

use App\Filament\Resources\Jadwals\Pages\ViewJadwal;
use App\Models\PesertaJadwal;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
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
                    ->dateTime(),
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
                            Grid::make(1)
                                ->inlineLabel()
                                ->schema([
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
                                        ->numeric(),
                                ])
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
