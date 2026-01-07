<?php

namespace App\Filament\Resources\PermintaanPenjemputans\Pages;

use App\Filament\Resources\PermintaanPenjemputans\PermintaanPenjemputanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPermintaanPenjemputans extends ListRecords
{
    protected static string $resource = PermintaanPenjemputanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
