<?php

namespace App\Filament\Resources\PermintaanPenjemputans\Pages;

use App\Filament\Resources\PermintaanPenjemputans\PermintaanPenjemputanResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPermintaanPenjemputan extends ViewRecord
{
    protected static string $resource = PermintaanPenjemputanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
