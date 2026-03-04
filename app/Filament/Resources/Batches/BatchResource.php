<?php

namespace App\Filament\Resources\Batches;

use App\Filament\Resources\Batches\Pages\CreateBatch;
use App\Filament\Resources\Batches\Pages\EditBatch;
use App\Filament\Resources\Batches\Pages\ListBatches;
use App\Filament\Resources\Batches\Pages\ViewBatch;
use App\Filament\Resources\Batches\Schemas\BatchForm;
use App\Filament\Resources\Batches\Schemas\BatchInfolist;
use App\Filament\Resources\Batches\Tables\BatchesTable;
use App\Filament\Resources\Batches\RelationManagers\PesertaRelationManager;
use App\Models\Batch;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class BatchResource extends Resource
{
    protected static ?string $model = Batch::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Batch';

    protected static string|UnitEnum|null $navigationGroup = 'Short Course';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return BatchForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BatchInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BatchesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            PesertaRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBatches::route('/'),
            'create' => CreateBatch::route('/create'),
            'view' => ViewBatch::route('/{record}'),
            'edit' => EditBatch::route('/{record}/edit'),
        ];
    }
}
