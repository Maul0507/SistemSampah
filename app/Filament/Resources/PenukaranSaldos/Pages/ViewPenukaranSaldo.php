<?php

namespace App\Filament\Resources\PenukaranSaldos\Pages;

use App\Filament\Resources\PenukaranSaldos\PenukaranSaldoResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPenukaranSaldo extends ViewRecord
{
    protected static string $resource = PenukaranSaldoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
