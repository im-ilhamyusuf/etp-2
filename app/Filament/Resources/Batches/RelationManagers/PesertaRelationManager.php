<?php

namespace App\Filament\Resources\Batches\RelationManagers;

use App\Filament\Resources\Batches\Pages\ViewBatch;
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
use Filament\Schemas\Components\Group;
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
    protected static string $relationship = 'pesertaBatch';

    protected static ?string $title = 'Peserta';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return $pageClass === ViewBatch::class;
    }

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
                TextColumn::make('pretest')
                    ->label('Pre-test'),
                TextColumn::make('posttest')
                    ->label('Post-test'),
                IconColumn::make('peserta.status_short_course')
                    ->label('Short Course')
                    ->boolean()
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
                                'pretest' => $record->pretest,
                                'posttest' => $record->posttest,
                                'short_course' => $record->peserta?->short_course,
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
                                Section::make('Short Course')
                                    ->schema([
                                        TextInput::make('pretest')
                                            ->label('Pre-test')
                                            ->numeric(),
                                        TextInput::make('posttest')
                                            ->label('Post-test')
                                            ->numeric(),
                                        DateTimePicker::make('short_course'),
                                    ])
                            ])
                                ->inlineLabel()
                        ])
                        ->modalSubmitActionLabel("Simpan")
                        ->action(function ($record, $data) {

                            $record->update([
                                'pretest' => $data['pretest'],
                                'posttest' => $data['posttest'],
                            ]);

                            // update relasi peserta
                            $record->peserta->update([
                                'short_course' => $data['short_course'] ?? null,
                            ]);

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
