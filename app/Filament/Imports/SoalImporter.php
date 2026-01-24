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
        ];
    }

    public function resolveRecord(): Soal
    {
        return new Soal();
    }

    protected function beforeSave(): void
    {
        $this->record->bank_soal_id = $this->options['bank_soal_id'] ?? null;
    }

    protected function afterSave(): void
    {
        if (! $this->record->exists) {
            return;
        }

        // pastikan hanya create sekali
        if ($this->record->soalJawaban()->exists()) {
            return;
        }

        $row = $this->data;

        $this->record->soalJawaban()->createMany([
            ['jawaban' => $row['benar'] ?? null,   'benar' => true],
            ['jawaban' => $row['salah_1'] ?? null, 'benar' => false],
            ['jawaban' => $row['salah_2'] ?? null, 'benar' => false],
            ['jawaban' => $row['salah_3'] ?? null, 'benar' => false],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Impor soal selesai. ' . Number::format($import->successful_rows) . ' baris berhasil diimpor..';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' baris gagal diimpor.';
        }

        return $body;
    }
}
