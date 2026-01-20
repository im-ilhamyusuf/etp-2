<?php

namespace App\Filament\Resources\Biodatas;

use App\Filament\Resources\Biodatas\Pages\CreateBiodata;
use App\Filament\Resources\Biodatas\Pages\EditBiodata;
use App\Filament\Resources\Biodatas\Pages\ListBiodatas;
use App\Filament\Resources\Biodatas\Schemas\BiodataForm;
use App\Filament\Resources\Biodatas\Tables\BiodatasTable;
use App\Models\Biodata;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class BiodataResource extends Resource
{
    protected static ?string $model = Biodata::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedIdentification;

    protected static ?string $recordTitleAttribute = 'nik';

    protected static string | UnitEnum | null $navigationGroup = 'Master';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return BiodataForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BiodatasTable::configure($table);
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
            'index' => ListBiodatas::route('/'),
            'create' => CreateBiodata::route('/create'),
            'edit' => EditBiodata::route('/{record}/edit'),
        ];
    }
}
