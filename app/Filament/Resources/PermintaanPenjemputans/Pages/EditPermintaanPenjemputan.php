<?php

namespace App\Filament\Resources\PermintaanPenjemputans\Pages;

use App\Filament\Resources\PermintaanPenjemputans\PermintaanPenjemputanResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPermintaanPenjemputan extends EditRecord
{
    protected static string $resource = PermintaanPenjemputanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
