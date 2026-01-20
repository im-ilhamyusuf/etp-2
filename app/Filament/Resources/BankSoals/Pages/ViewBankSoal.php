<?php

namespace App\Filament\Resources\BankSoals\Pages;

use App\Filament\Resources\BankSoals\BankSoalResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewBankSoal extends ViewRecord
{
    protected static string $resource = BankSoalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
