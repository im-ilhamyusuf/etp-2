<?php

namespace App\Filament\Resources\BankSoals\Pages;

use App\Filament\Resources\BankSoals\BankSoalResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditBankSoal extends EditRecord
{
    protected static string $resource = BankSoalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
