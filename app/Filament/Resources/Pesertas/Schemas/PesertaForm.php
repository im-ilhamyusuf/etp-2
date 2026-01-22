<?php

namespace App\Filament\Resources\Pesertas\Schemas;

use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class PesertaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->columnSpan(2)
                    ->schema([
                        Section::make('Akun')
                            ->icon(Heroicon::OutlinedUserCircle)
                            ->collapsed(false)
                            ->columns(2)
                            ->schema([
                                Select::make('user_id')
                                    ->label("Akun User")
                                    ->options(User::user()->pluck('name', 'id'))
                                    ->required(),
                                TextInput::make('no_peserta')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->placeholder("No peserta otomatis dibuatkan oleh sistem"),
                            ]),

                        Section::make('Biodata')
                            ->icon(Heroicon::OutlinedIdentification)
                            ->collapsed(false)
                            ->columns(2)
                            ->schema([
                                TextInput::make('nik')
                                    ->required(),
                                Select::make('jenis_kelamin')
                                    ->options(['L' => 'Laki-laki', 'P' => 'Perempuan'])
                                    ->required()
                                    ->searchable(),
                                TextInput::make('tempat_lahir')
                                    ->required(),
                                DatePicker::make('tanggal_lahir')
                                    ->required(),
                                Select::make('agama')
                                    ->options([
                                        'Islam' => 'Islam',
                                        'Kristen' => 'Kristen',
                                        'Katolik' => 'Katolik',
                                        'Hindu' => 'Hindu',
                                        'Buddha' => 'Buddha',
                                        'Konghucu' => 'Konghucu',
                                    ])
                                    ->required()
                                    ->searchable(),
                                Select::make('kewarganegaraan')
                                    ->options(['WNI' => 'WNI', 'WNA' => 'WNA'])
                                    ->required()
                                    ->searchable(),
                                TextInput::make('bahasa'),
                                TextInput::make('no_hp')
                                    ->required(),
                                Textarea::make('alamat')
                                    ->required()
                                    ->columnSpanFull(),
                            ]),

                        Section::make('Pendidikan')
                            ->icon(Heroicon::OutlinedAcademicCap)
                            ->collapsed(false)
                            ->columns(2)
                            ->schema([
                                Select::make('pendidikan_terakhir')
                                    ->options([
                                        'Tidak Tamat SD' => 'Tidak tamat SD',
                                        'SD' => 'SD',
                                        'SMP' => 'SMP',
                                        'SMA/SMK' => 'SMA/SMK',
                                        'D3' => 'D3',
                                        'S1' => 'S1',
                                        'S2' => 'S2',
                                        'S3' => 'S3',
                                    ])
                                    ->required()
                                    ->searchable(),
                                TextInput::make('tahun_lulus'),
                            ]),

                        Section::make('Pekerjaan')
                            ->icon(Heroicon::OutlinedBriefcase)
                            ->collapsed(false)
                            ->columns(2)
                            ->schema([
                                TextInput::make('pekerjaan'),
                                TextInput::make('instansi'),
                                TextInput::make('nim')
                                    ->label("NIM")
                                    ->belowContent(components: "Isi jika pekerjaan Mahasiswa"),
                                TextInput::make('nidn')
                                    ->label("NIDN")
                                    ->belowContent("Isi jika pekerjaan Dosen"),
                            ]),
                    ])
                    ->columnOrder([
                        'default' => 2,
                        'lg' => 1
                    ]),

                    Section::make("Pas Foto")
                        ->icon(Heroicon::OutlinedPhoto)
                        ->schema([
                            FileUpload::make('foto')
                                ->image()
                                ->imageEditor()
                                ->imageCropAspectRatio('2:3')
                                ->disk('public')
                                ->directory('peserta')
                        ])
                        ->columnOrder([
                            'default' => 1,
                            'lg' => 2
                        ]),
            ])
            ->columns(3);
    }
}
