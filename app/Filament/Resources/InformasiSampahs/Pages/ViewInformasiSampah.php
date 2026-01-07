<?php

namespace App\Filament\Resources\InformasiSampahs\Pages;

use App\Filament\Resources\InformasiSampahs\InformasiSampahResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewInformasiSampah extends ViewRecord
{
    protected static string $resource = InformasiSampahResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
