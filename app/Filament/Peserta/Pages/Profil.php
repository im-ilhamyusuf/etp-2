<?php

namespace App\Filament\Peserta\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;

class Profil extends Page implements HasSchemas
{
    use InteractsWithSchemas;

    protected string $view = 'filament.peserta.pages.profil';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedIdentification;

    public ?array $data = [];

    public function mount()
    {
        $user = Auth::user();

        $this->form->fill([
            'user' => [
                'name'  => $user->name,
                'email' => $user->email,
            ],

            'peserta' => [
                'no_peserta' => $user->peserta?->no_peserta,
                'nik' => $user->peserta?->nik,
                'jenis_kelamin' => $user->peserta?->jenis_kelamin,
                'tempat_lahir' => $user->peserta?->tempat_lahir,
                'tanggal_lahir' => $user->peserta?->tanggal_lahir,
                'agama' => $user->peserta?->agama,
                'pendidikan_terakhir' => $user->peserta?->pendidikan_terakhir,
                'tahun_lulus' => $user->peserta?->tahun_lulus,
                'pekerjaan' => $user->peserta?->pekerjaan,
                'instansi' => $user->peserta?->instansi,
                'nim' => $user->peserta?->nim,
                'nidn' => $user->peserta?->nidn,
                'kewarganegaraan' => $user->peserta?->kewarganegaraan,
                'bahasa' => $user->peserta?->bahasa,
                'no_hp' => $user->peserta?->no_hp,
                'alamat' => $user->peserta?->alamat,
                'foto' => $user->peserta?->foto,
            ],
        ]);
    }

    public function form(Schema $schema)
    {
        return $schema
            ->statePath('data')
            ->columns(3)
            ->schema([
                Group::make()
                    ->columnSpan(2)
                    ->schema([
                        Section::make('Akun')
                            ->icon(Heroicon::OutlinedUserCircle)
                            ->collapsed(false)
                            ->columns(2)
                            ->schema([
                                TextInput::make('peserta.no_peserta')
                                    ->disabled()
                                    ->dehydrated(false),
                                TextInput::make('user.name')
                                    ->label("Nama")
                                    ->required(),
                                TextInput::make('user.email')
                                    ->required(),
                                TextInput::make('user.password')
                                    ->password()
                                    ->dehydrated(fn ($state) => filled($state)),
                            ]),

                        Section::make('Biodata')
                            ->icon(Heroicon::OutlinedIdentification)
                            ->collapsed(false)
                            ->columns(2)
                            ->schema([
                                TextInput::make('peserta.nik')
                                    ->required(),
                                Select::make('peserta.jenis_kelamin')
                                    ->options(['L' => 'Laki-laki', 'P' => 'Perempuan'])
                                    ->required()
                                    ->searchable(),
                                TextInput::make('peserta.tempat_lahir')
                                    ->required(),
                                DatePicker::make('peserta.tanggal_lahir')
                                    ->required(),
                                Select::make('peserta.agama')
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
                                Select::make('peserta.kewarganegaraan')
                                    ->options(['WNI' => 'WNI', 'WNA' => 'WNA'])
                                    ->required()
                                    ->searchable(),
                                TextInput::make('peserta.bahasa'),
                                TextInput::make('peserta.no_hp')
                                    ->required(),
                                Textarea::make('peserta.alamat')
                                    ->required()
                                    ->columnSpanFull(),
                            ]),

                        Section::make('Pendidikan')
                            ->icon(Heroicon::OutlinedAcademicCap)
                            ->collapsed(false)
                            ->columns(2)
                            ->schema([
                                Select::make('peserta.pendidikan_terakhir')
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
                                TextInput::make('peserta.tahun_lulus'),
                            ]),

                        Section::make('Pekerjaan')
                            ->icon(Heroicon::OutlinedBriefcase)
                            ->collapsed(false)
                            ->columns(2)
                            ->schema([
                                TextInput::make('peserta.pekerjaan'),
                                TextInput::make('peserta.instansi'),
                                TextInput::make('peserta.nim')
                                    ->label("NIM")
                                    ->belowContent(components: "Isi jika pekerjaanmu Mahasiswa"),
                                TextInput::make('peserta.nidn')
                                    ->label("NIDN")
                                    ->belowContent("Isi jika pekerjaan Dosen"),
                            ]),

                        Action::make('save')
                            ->label('Simpan Perubahan')
                            ->color('primary')
                            ->requiresConfirmation()
                            ->action(fn () => $this->save()),
                    ]),

                    Section::make("Pas Foto")
                        ->icon(Heroicon::OutlinedPhoto)
                        ->schema([
                            FileUpload::make('peserta.foto')
                                ->image()
                                ->imageEditor()
                                ->imageCropAspectRatio('2:3')
                                ->disk('public')
                                ->directory('peserta')
                        ])
            ]);
    }

    public function save(): void
    {
        $user = Auth::user();
        $data = $this->form->getState();

        // update user
        $user->update($data['user']);

        // update / create biodata
        $user->peserta()->updateOrCreate(
            ['user_id' => $user->id],
            $data['peserta']
        );

        Notification::make()
            ->title('Data berhasil diperbarui')
            ->success()
            ->send();
    }
}
