<?php

namespace App\Filament\Resources\Pesertas;

use App\Filament\Resources\Pesertas\Pages\CreatePeserta;
use App\Filament\Resources\Pesertas\Pages\EditPeserta;
use App\Filament\Resources\Pesertas\Pages\ListPesertas;
use App\Filament\Resources\Pesertas\Schemas\PesertaForm;
use App\Filament\Resources\Pesertas\Tables\PesertasTable;
use App\Models\Peserta;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

use UnitEnum;

class PesertaResource extends Resource
{
    protected static ?string $model = Peserta::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedIdentification;

    protected static ?string $recordTitleAttribute = 'no_peserta';

    protected static string | UnitEnum | null $navigationGroup = 'Master';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return PesertaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PesertasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPesertas::route('/'),
            'create' => CreatePeserta::route('/create'),
            'edit' => EditPeserta::route('/{record}/edit'),
        ];
    }
}
