<?php

namespace App\Filament\Resources\InformasiSampahs\Pages;

use App\Filament\Resources\InformasiSampahs\InformasiSampahResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListInformasiSampahs extends ListRecords
{
    protected static string $resource = InformasiSampahResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
