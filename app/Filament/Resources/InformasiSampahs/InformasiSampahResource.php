<?php

namespace App\Filament\Resources\InformasiSampahs;

use App\Filament\Resources\InformasiSampahs\Pages\CreateInformasiSampah;
use App\Filament\Resources\InformasiSampahs\Pages\EditInformasiSampah;
use App\Filament\Resources\InformasiSampahs\Pages\ListInformasiSampahs;
use App\Filament\Resources\InformasiSampahs\Pages\ViewInformasiSampah;
use App\Filament\Resources\InformasiSampahs\Schemas\InformasiSampahForm;
use App\Filament\Resources\InformasiSampahs\Schemas\InformasiSampahInfolist;
use App\Filament\Resources\InformasiSampahs\Tables\InformasiSampahsTable;
use App\Models\InformasiSampah;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class InformasiSampahResource extends Resource
{
    protected static ?string $model = InformasiSampah::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'yes';

    public static function form(Schema $schema): Schema
    {
        return InformasiSampahForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return InformasiSampahInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InformasiSampahsTable::configure($table);
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
            'index' => ListInformasiSampahs::route('/'),
            'create' => CreateInformasiSampah::route('/create'),
            'view' => ViewInformasiSampah::route('/{record}'),
            'edit' => EditInformasiSampah::route('/{record}/edit'),
        ];
    }
}
