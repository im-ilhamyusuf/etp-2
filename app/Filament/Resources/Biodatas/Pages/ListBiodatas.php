<?php

namespace App\Filament\Resources\Biodatas\Pages;

use App\Filament\Resources\Biodatas\BiodataResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBiodatas extends ListRecords
{
    protected static string $resource = BiodataResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
