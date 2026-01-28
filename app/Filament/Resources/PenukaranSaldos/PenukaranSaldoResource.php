<?php

namespace App\Filament\Resources\PenukaranSaldos;

use App\Filament\Resources\PenukaranSaldos\Pages\CreatePenukaranSaldo;
use App\Filament\Resources\PenukaranSaldos\Pages\EditPenukaranSaldo;
use App\Filament\Resources\PenukaranSaldos\Pages\ListPenukaranSaldos;
use App\Filament\Resources\PenukaranSaldos\Pages\ViewPenukaranSaldo;
use App\Filament\Resources\PenukaranSaldos\Schemas\PenukaranSaldoForm;
use App\Filament\Resources\PenukaranSaldos\Schemas\PenukaranSaldoInfolist;
use App\Filament\Resources\PenukaranSaldos\Tables\PenukaranSaldosTable;
use App\Models\PenukaranSaldo;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PenukaranSaldoResource extends Resource
{
    protected static ?string $model = PenukaranSaldo::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'PenukaranSaldo';

    public static function form(Schema $schema): Schema
    {
        return PenukaranSaldoForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PenukaranSaldoInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PenukaranSaldosTable::configure($table);
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
            'index' => ListPenukaranSaldos::route('/'),
            'create' => CreatePenukaranSaldo::route('/create'),
            'view' => ViewPenukaranSaldo::route('/{record}'),
            'edit' => EditPenukaranSaldo::route('/{record}/edit'),
        ];
    }
}
