<?php

namespace App\Filament\Resources\Pesertas\Schemas;

use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class PesertaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make("Formulir Biodata Peserta")
                    ->icon(Heroicon::OutlinedDocumentText)
                    ->columnSpan(2)
                    ->columns(2)
                    ->schema([
                        Select::make('user_id')
                            ->label("Akun user")
                            ->options(User::peserta()->pluck('name', 'id'))
                            ->searchable(),
                        TextInput::make('no_peserta')
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('Otomatis dibuat sistem'),
                        Select::make('jenis_kelamin')
                            ->options(['L' => 'Laki-laki', 'P' => 'Perempuan'])
                            ->required()
                            ->searchable(),
                        TextInput::make('nik')
                            ->label("NIK")
                            ->required()
                            ->unique(),
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
                        TextInput::make('pekerjaan'),
                        TextInput::make('instansi'),
                        TextInput::make('nim')
                            ->label("NIM"),
                        TextInput::make('nidn')
                            ->label("NIDN"),
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
            ])
            ->columns(3);
    }
}
