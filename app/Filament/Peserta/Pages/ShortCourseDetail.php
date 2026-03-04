<?php

namespace App\Filament\Peserta\Pages;

use App\Models\Materi;
use App\Models\PesertaBatch;
use BackedEnum;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Pages\Page;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class ShortCourseDetail extends Page implements HasInfolists, HasTable
{
    use InteractsWithInfolists, InteractsWithTable;

    protected static ?string $slug = 'short-course/{record}';
    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.peserta.pages.short-course-detail';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    public PesertaBatch $record;

    public function mount(PesertaBatch $record): void
    {
        // Pastikan hanya peserta pemilik yang bisa akses
        abort_unless(
            $record->peserta_id === auth()->user()->peserta->id,
            403
        );

        $this->record = $record->load('batch');
    }

    public function courseInfolist(Schema $schema): Schema
    {
        return $schema
            ->record($this->record)
            ->schema([
                Section::make('Informasi Short Course')
                    ->columns([
                        'default' => 2,
                        'xl' => 4
                    ])
                    ->schema([
                        TextEntry::make('batch.judul')
                            ->label('Judul')
                            ->columnSpan([
                                'default' => 2,
                                'xl' => 1
                            ]),

                        TextEntry::make('batch.mulai')
                            ->label('Mulai')
                            ->dateTime('d F Y, H:i'),

                        TextEntry::make('batch.tutup')
                            ->label('Selesai')
                            ->dateTime('d F Y, H:i'),

                        IconEntry::make('batch.status')
                            ->label('Status Jadwal')
                            ->boolean(),
                    ]),

                Grid::make([
                    'default' => 1,
                    'md' => 2
                ])
                    ->schema([
                        Section::make('Nilai Pretest')
                            ->columns([
                                'default' => 2,
                                'xl' => 4
                            ])
                            ->schema([
                                TextEntry::make('poin_a1')
                                    ->label('Listening'),
                                TextEntry::make('poin_b1')
                                    ->label('Strcuture'),
                                TextEntry::make('poin_c1')
                                    ->label('Reading'),
                                TextEntry::make('nilai_akhir1')
                                    ->label('Nilai Akhir'),
                            ]),

                        Section::make('Nilai Posttest')
                            ->columns([
                                'default' => 2,
                                'xl' => 4
                            ])
                            ->schema([
                                TextEntry::make('poin_a2')
                                    ->label('Listening'),
                                TextEntry::make('poin_b2')
                                    ->label('Strcuture'),
                                TextEntry::make('poin_c2')
                                    ->label('Reading'),
                                TextEntry::make('nilai_akhir2')
                                    ->label('Nilai Akhir'),
                            ]),
                    ])
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Materi::query())
            ->columns([
                TextColumn::make('judul'),

                // PRETEST (selalu aktif)
                TextColumn::make('url_ujian_1')
                    ->label('Pretest')
                    ->url(fn($record) => $record->url_ujian_1)
                    ->formatStateUsing(fn() => 'Buka')
                    ->color(Color::Blue)
                    ->icon(Heroicon::Link)
                    ->openUrlInNewTab(),

                // VIDEO (tergantung sesi)
                TextColumn::make('url_video')
                    ->label('Video')
                    ->formatStateUsing(
                        fn($record) =>
                        $this->isSesiUnlocked($record) ? 'Buka' : 'Terkunci'
                    )
                    ->url(
                        fn($record) =>
                        $this->isSesiUnlocked($record) ? $record->url_video : null
                    )
                    ->color(
                        fn($record) =>
                        $this->isSesiUnlocked($record) ? Color::Blue : Color::Gray
                    )
                    ->icon(Heroicon::Link)
                    ->openUrlInNewTab(),

                // POSTTEST (tergantung sesi + batch aktif)
                TextColumn::make('url_ujian_2')
                    ->label('Posttest')
                    ->formatStateUsing(
                        fn($record) =>
                        $this->isPosttestUnlocked($record) ? 'Buka' : 'Terkunci'
                    )
                    ->url(
                        fn($record) =>
                        $this->isPosttestUnlocked($record) ? $record->url_ujian_2 : null
                    )
                    ->color(
                        fn($record) =>
                        $this->isPosttestUnlocked($record) ? Color::Blue : Color::Gray
                    )
                    ->icon(Heroicon::Link)
                    ->openUrlInNewTab(),
            ]);
    }

    protected function isSesiUnlocked($materi): bool
    {
        return match ($materi->jenis) {
            'listening' => $this->record->poin_a1 != 0,
            'structure' => $this->record->poin_b1 != 0,
            'reading' => $this->record->poin_c1 != 0,
            default => false,
        };
    }

    protected function isPosttestUnlocked($materi): bool
    {
        return $this->isSesiUnlocked($materi)
            && $this->record->batch->status === true;
    }
}
