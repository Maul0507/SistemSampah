<?php

namespace App\Filament\Resources\InformasiSampahs\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;

class InformasiSampahForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('jenis_sampah')
                    ->label('Jenis Sampah')
                    ->required(),

                TextInput::make('harga')
                    ->label('Harga (Rp)')
                    ->required()
                    ->numeric(),

                // ✅ Ubah dari TextInput ke FileUpload
                FileUpload::make('gambar')
                    ->label('Gambar')
                    ->image()
                    ->directory('informasi_sampah') // simpan di storage/app/public/informasi_sampah
                    ->disk('public')
                    ->visibility('public')
                    ->preserveFilenames(),

                Textarea::make('deskripsi')
                    ->label('Deskripsi')
                    ->columnSpanFull(),
            ]);
    }
}
