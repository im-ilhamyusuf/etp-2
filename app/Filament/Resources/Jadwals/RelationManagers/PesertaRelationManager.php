<?php

namespace App\Filament\Resources\Jadwals\RelationManagers;

use App\Filament\Resources\Jadwals\Pages\ViewJadwal;
use Filament\Actions\Action;
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
            ->recordTitleAttribute('peserta_id')
            ->columns([
                TextColumn::make('no_peserta')
                    ->getStateUsing(fn($record) => $record->peserta?->no_peserta)
                    ->width('150px'),
                TextColumn::make('nama')
                    ->getStateUsing(fn($record) => $record->peserta?->user?->name),
                TextColumn::make('status')
                    ->getStateUsing(fn($record) => $record->peserta?->status)
                    ->formatStateUsing(fn($state) => ucwords($state))
                    ->width('150px'),
                TextColumn::make('jenis_kelamin')
                    ->getStateUsing(fn($record) => $record->peserta?->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan')
                    ->width('150px'),
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
                    )
                    ->width('150px'),
                IconColumn::make('status_validasi')
                    ->getStateUsing(fn($record) => $record->validasi != null)
                    ->boolean()
                    ->width('150px'),
            ])
            ->filters([
                //
            ])
            ->headerActions([])
            ->recordActions([
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
            ->toolbarActions([]);
    }
}
