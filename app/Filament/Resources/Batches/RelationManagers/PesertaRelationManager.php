<?php

namespace App\Filament\Resources\Batches\RelationManagers;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class PesertaRelationManager extends RelationManager
{
    protected static string $relationship = 'pesertaBatch';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('no_peserta')
                    ->required()
                    ->maxLength(255),
            ]);
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
                    ->label('Status Validasi')
                    ->getStateUsing(fn($record) => $record->validasi != null)
                    ->boolean(),
                TextColumn::make('Pretest')
                    ->getStateUsing(fn($record) => "$record->poin_a1,$record->poin_b1,$record->poin_c1,$record->nilai_akhir1"),
                TextColumn::make('Posttest')
                    ->getStateUsing(fn($record) => "$record->poin_a2,$record->poin_b2,$record->poin_c2,$record->nilai_akhir2"),
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
                    Action::make('detailShortCourse')
                        ->icon(Heroicon::NumberedList)
                        ->modalWidth(Width::ExtraLarge)
                        ->mountUsing(function (Schema $form, $record) {
                            $form->fill([
                                'poin_a1' => $record->poin_a1,
                                'poin_b1' => $record->poin_b1,
                                'poin_c1' => $record->poin_c1,
                                'nilai_akhir1' => $record->nilai_akhir1,
                                'poin_a2' => $record->poin_a2,
                                'poin_b2' => $record->poin_b2,
                                'poin_c2' => $record->poin_c2,
                                'nilai_akhir2' => $record->nilai_akhir2,
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
                                Section::make('Prestest')
                                    ->schema([
                                        TextInput::make('poin_a1')
                                            ->label("Listening")
                                            ->numeric(),
                                        TextInput::make('poin_b1')
                                            ->label("Structure")
                                            ->numeric(),
                                        TextInput::make('poin_c1')
                                            ->label("Reading")
                                            ->numeric(),
                                        TextInput::make('nilai_akhir1')
                                            ->label("Nilai Akhir")
                                            ->numeric(),
                                    ]),

                                Section::make('Posttest')
                                    ->schema([
                                        TextInput::make('poin_a2')
                                            ->label("Listening")
                                            ->numeric(),
                                        TextInput::make('poin_b2')
                                            ->label("Structure")
                                            ->numeric(),
                                        TextInput::make('poin_c2')
                                            ->label("Reading")
                                            ->numeric(),
                                        TextInput::make('nilai_akhir2')
                                            ->label("Nilai Akhir")
                                            ->numeric(),
                                    ])
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
