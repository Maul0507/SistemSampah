<?php

namespace App\Filament\Resources\InformasiSampahs\Pages;

use App\Filament\Resources\InformasiSampahs\InformasiSampahResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditInformasiSampah extends EditRecord
{
    protected static string $resource = InformasiSampahResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
