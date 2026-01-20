<?php

namespace App\Filament\Resources\Biodatas\Pages;

use App\Filament\Resources\Biodatas\BiodataResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBiodata extends EditRecord
{
    protected static string $resource = BiodataResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
