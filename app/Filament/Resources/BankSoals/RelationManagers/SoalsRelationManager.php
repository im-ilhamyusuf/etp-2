<?php

namespace App\Filament\Resources\BankSoals\RelationManagers;

use App\Filament\Imports\SoalImporter;
use App\Filament\Resources\BankSoals\Pages\ViewBankSoal;
use App\Models\SoalJawaban;
use DB;
use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ImportAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Collection;
use Filament\Resources\Pages\ViewRecord;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class SoalsRelationManager extends RelationManager
{
    protected static string $relationship = 'soal';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return $pageClass === ViewBankSoal::class;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Textarea::make('soal')
                    ->columnSpan(2)
                    ->belowContent("Isi '-' jika tidak ada soal dalam bentuk text")
                    ->required(),
                FileUpload::make('gambar')
                    ->disk('public')
                    ->image()
                    ->imageEditor()
                    ->directory('soal'),
                FileUpload::make('audio')
                    ->disk('public')
                    ->directory('soal')
                    ->acceptedFileTypes([
                        'audio/mpeg',
                        'audio/wav',
                        'audio/ogg',
                        'audio/mp4',
                    ]),
                TextInput::make("jawaban_benar")
                    ->columnSpan(2),
                TextInput::make("jawaban_salah_1")
                    ->columnSpan(2),
                TextInput::make("jawaban_salah_2")
                    ->columnSpan(2),
                TextInput::make("jawaban_salah_3")
                    ->columnSpan(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('soal')
            ->columns([
                TextColumn::make('row_index')
                    ->label("No")
                    ->rowIndex()
                    ->width('20px'),
                TextColumn::make('soal')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('gambar')
                    ->label('Gambar')
                    ->color(color: Color::Blue)
                    ->url(fn($record) => $record->gambar ? asset('storage/' . $record->gambar) : null)
                    ->openUrlInNewTab()
                    ->formatStateUsing(fn() => 'Lihat Gambar')
                    ->width('150px'),
                TextColumn::make('audio')
                    ->label('Audio')
                    ->color(Color::Blue)
                    ->url(fn($record) => $record->audio ? asset('storage/' . $record->audio) : null)
                    ->openUrlInNewTab()
                    ->formatStateUsing(fn() => 'Putar Audio')
                    ->width('150px')
            ])
            ->filters([
                //
            ])
            ->headerActions([
                ImportAction::make()
                    ->importer(SoalImporter::class)
                    ->options([
                        'bank_soal_id' => $this->getOwnerRecord()->id,
                    ]),
                CreateAction::make()
                    ->using(function (array $data, RelationManager $livewire) {
                        $soal = $livewire->getRelationship()->create([
                            'soal'   => $data['soal'],
                            'gambar' => $data['gambar'] ?? null,
                            'audio'  => $data['audio'] ?? null,
                        ]);

                        $soal->soalJawaban()->createMany([
                            ['jawaban' => $data['jawaban_benar'],   'benar' => true],
                            ['jawaban' => $data['jawaban_salah_1'], 'benar' => false],
                            ['jawaban' => $data['jawaban_salah_2'], 'benar' => false],
                            ['jawaban' => $data['jawaban_salah_3'], 'benar' => false],
                        ]);

                        return $soal;
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->mutateRecordDataUsing(function (array $data, Model $record) {

                        $jawaban = $record->soalJawaban;

                        $benar  = $jawaban->firstWhere('benar', true);
                        $salah  = $jawaban->where('benar', false)->values();

                        $data['jawaban_benar']   = $benar?->jawaban;
                        $data['jawaban_salah_1'] = $salah[0]->jawaban ?? null;
                        $data['jawaban_salah_2'] = $salah[1]->jawaban ?? null;
                        $data['jawaban_salah_3'] = $salah[2]->jawaban ?? null;

                        return $data;
                    })
                    ->using(function (Model $record, array $data) {
                        $record->update([
                            'soal'   => $data['soal'],
                            'gambar' => $data['gambar'] ?? null,
                            'audio'  => $data['audio'] ?? null,
                        ]);

                        // Hapus jawaban lama
                        $record->soalJawaban()->delete();

                        // Simpan ulang
                        $record->soalJawaban()->createMany([
                            ['jawaban' => $data['jawaban_benar'],   'benar' => true],
                            ['jawaban' => $data['jawaban_salah_1'], 'benar' => false],
                            ['jawaban' => $data['jawaban_salah_2'], 'benar' => false],
                            ['jawaban' => $data['jawaban_salah_3'], 'benar' => false],
                        ]);

                        return $record;
                    }),
                DeleteAction::make()
                    ->using(function (Model $record) {
                        $record->soalJawaban()->delete();
                        $record->delete();

                        return $record;
                    }),
            ])
            ->toolbarActions([
                DeleteBulkAction::make()
                    ->using(function ($records) {
                        foreach ($records as $record) {
                            $record->soalJawaban()->delete();
                            $record->delete();
                        }

                        return $records;
                    }),
            ]);
    }
}
