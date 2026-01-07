<?php

namespace App\Filament\Resources\PenukaranSaldos\Pages;

use App\Filament\Resources\PenukaranSaldos\PenukaranSaldoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPenukaranSaldos extends ListRecords
{
    protected static string $resource = PenukaranSaldoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
