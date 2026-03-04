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
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
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
                    ->label('Status Validasi')
                    ->getStateUsing(fn($record) => $record->validasi != null)
                    ->boolean(),
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
                    DeleteAction::make(),
                ])
            ])
            ->toolbarActions([]);
    }
}
