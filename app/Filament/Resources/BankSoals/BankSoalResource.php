<?php

namespace App\Filament\Resources\BankSoals;

use App\Filament\Resources\BankSoals\Pages\CreateBankSoal;
use App\Filament\Resources\BankSoals\Pages\EditBankSoal;
use App\Filament\Resources\BankSoals\Pages\ListBankSoals;
use App\Filament\Resources\BankSoals\Pages\ViewBankSoal;
use App\Filament\Resources\BankSoals\RelationManagers\SoalsRelationManager;
use App\Filament\Resources\BankSoals\Schemas\BankSoalForm;
use App\Filament\Resources\BankSoals\Schemas\BankSoalInfolist;
use App\Filament\Resources\BankSoals\Tables\BankSoalsTable;
use App\Models\BankSoal;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class BankSoalResource extends Resource
{
    protected static ?string $model = BankSoal::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFolderOpen;

    protected static ?string $recordTitleAttribute = 'nama';

    protected static string | UnitEnum | null $navigationGroup = 'Master';
    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return BankSoalForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BankSoalInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BankSoalsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            SoalsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBankSoals::route('/'),
            'create' => CreateBankSoal::route('/create'),
            'view' => ViewBankSoal::route('/{record}'),
            'edit' => EditBankSoal::route('/{record}/edit'),
        ];
    }
}
