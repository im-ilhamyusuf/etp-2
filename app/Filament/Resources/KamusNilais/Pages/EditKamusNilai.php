<?php

namespace App\Filament\Resources\KamusNilais\Pages;

use App\Filament\Resources\KamusNilais\KamusNilaiResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditKamusNilai extends EditRecord
{
    protected static string $resource = KamusNilaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
