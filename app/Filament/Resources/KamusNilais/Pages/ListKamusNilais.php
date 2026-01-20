<?php

namespace App\Filament\Resources\KamusNilais\Pages;

use App\Filament\Resources\KamusNilais\KamusNilaiResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListKamusNilais extends ListRecords
{
    protected static string $resource = KamusNilaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
