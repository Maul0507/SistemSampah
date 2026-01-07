<?php

namespace App\Filament\Resources\PenukaranSaldos\Pages;

use App\Filament\Resources\PenukaranSaldos\PenukaranSaldoResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPenukaranSaldo extends EditRecord
{
    protected static string $resource = PenukaranSaldoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
