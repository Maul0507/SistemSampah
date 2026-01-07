<?php

namespace App\Filament\Resources\PermintaanPenjemputans;

use App\Filament\Resources\PermintaanPenjemputans\Pages\CreatePermintaanPenjemputan;
use App\Filament\Resources\PermintaanPenjemputans\Pages\EditPermintaanPenjemputan;
use App\Filament\Resources\PermintaanPenjemputans\Pages\ListPermintaanPenjemputans;
use App\Filament\Resources\PermintaanPenjemputans\Pages\ViewPermintaanPenjemputan;
use App\Filament\Resources\PermintaanPenjemputans\Schemas\PermintaanPenjemputanForm;
use App\Filament\Resources\PermintaanPenjemputans\Schemas\PermintaanPenjemputanInfolist;
use App\Filament\Resources\PermintaanPenjemputans\Tables\PermintaanPenjemputansTable;
use App\Models\PermintaanPenjemputan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PermintaanPenjemputanResource extends Resource
{
    protected static ?string $model = PermintaanPenjemputan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Penjemputan';

    public static function form(Schema $schema): Schema
    {
        return PermintaanPenjemputanForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PermintaanPenjemputanInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PermintaanPenjemputansTable::configure($table);
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
            'index' => ListPermintaanPenjemputans::route('/'),
            'create' => CreatePermintaanPenjemputan::route('/create'),
            'view' => ViewPermintaanPenjemputan::route('/{record}'),
            'edit' => EditPermintaanPenjemputan::route('/{record}/edit'),
        ];
    }
}
