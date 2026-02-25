<?php

namespace App\Filament\Resources\Materis;

use App\Filament\Resources\Materis\Pages\CreateMateri;
use App\Filament\Resources\Materis\Pages\EditMateri;
use App\Filament\Resources\Materis\Pages\ListMateris;
use App\Filament\Resources\Materis\Schemas\MateriForm;
use App\Filament\Resources\Materis\Tables\MaterisTable;
use App\Models\Materi;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class MateriResource extends Resource
{
    protected static ?string $model = Materi::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLightBulb;

    protected static ?string $recordTitleAttribute = 'judul';

    protected static string|UnitEnum|null $navigationGroup = 'Short Course';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return MateriForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MaterisTable::configure($table);
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
            'index' => ListMateris::route('/'),
            'create' => CreateMateri::route('/create'),
            'edit' => EditMateri::route('/{record}/edit'),
        ];
    }
}
