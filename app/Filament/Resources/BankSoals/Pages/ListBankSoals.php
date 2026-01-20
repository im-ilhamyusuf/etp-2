<?php

namespace App\Filament\Resources\BankSoals\Pages;

use App\Filament\Resources\BankSoals\BankSoalResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBankSoals extends ListRecords
{
    protected static string $resource = BankSoalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
