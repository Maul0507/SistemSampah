<?php

namespace App\Filament\Resources\PenukaranSaldos\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PenukaranSaldoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                TextInput::make('amount')
                    ->required()
                    ->numeric(),
                TextInput::make('bank_name')
                    ->required(),
                TextInput::make('account_number')
                    ->required(),
                TextInput::make('account_name')
                    ->required(),
                Select::make('status')
                    ->options([
            'pending' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'success' => 'Success',
        ])
                    ->default('pending')
                    ->required(),
            ]);
    }
}
