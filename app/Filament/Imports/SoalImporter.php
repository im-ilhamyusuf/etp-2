<?php

namespace App\Filament\Imports;

use App\Models\Soal;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class SoalImporter extends Importer
{
    protected static ?string $model = Soal::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('soal'),
            ImportColumn::make('benar'),
            ImportColumn::make('salah_1'),
            ImportColumn::make('salah_2'),
            ImportColumn::make('salah_3'),
        ];
    }

    public function resolveRecord(): Soal
    {
        return new Soal();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Impor soal selesai. ' . Number::format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' baris berhasil diimpor..';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' baris gagal diimpor.';
        }

        return $body;
    }
}
