<?php

namespace App\Filament\Resources\KamusNilais;

use App\Filament\Resources\KamusNilais\Pages\CreateKamusNilai;
use App\Filament\Resources\KamusNilais\Pages\EditKamusNilai;
use App\Filament\Resources\KamusNilais\Pages\ListKamusNilais;
use App\Filament\Resources\KamusNilais\Schemas\KamusNilaiForm;
use App\Filament\Resources\KamusNilais\Tables\KamusNilaisTable;
use App\Models\KamusNilai;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class KamusNilaiResource extends Resource
{
    protected static ?string $model = KamusNilai::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookmark;

    protected static ?string $recordTitleAttribute = 'jumlah_benar';

    protected static string | UnitEnum | null $navigationGroup = 'Master';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return KamusNilaiForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KamusNilaisTable::configure($table);
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
            'index' => ListKamusNilais::route('/'),
            'create' => CreateKamusNilai::route('/create'),
            'edit' => EditKamusNilai::route('/{record}/edit'),
        ];
    }
}
