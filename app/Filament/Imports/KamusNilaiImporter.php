<?php

namespace App\Filament\Imports;

use App\Models\KamusNilai;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class KamusNilaiImporter extends Importer
{
    protected static ?string $model = KamusNilai::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('jumlah_benar')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('listening')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('structure')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('reading')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
        ];
    }

    public function resolveRecord(): KamusNilai
    {
        return new KamusNilai();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Impor kamus nilai selesai. '
            . Number::format($import->successful_rows)
            . ' baris berhasil diimpor.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '
                . Number::format($failedRowsCount)
                . ' baris gagal diimpor.';
        }

        return $body;
    }
}
