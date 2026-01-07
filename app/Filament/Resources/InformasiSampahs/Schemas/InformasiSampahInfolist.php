<?php

namespace App\Filament\Resources\InformasiSampahs\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class InformasiSampahInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('jenis_sampah'),
                TextEntry::make('harga')
                    ->numeric(),
                TextEntry::make('gambar')
                    ->placeholder('-'),
                TextEntry::make('deskripsi')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
