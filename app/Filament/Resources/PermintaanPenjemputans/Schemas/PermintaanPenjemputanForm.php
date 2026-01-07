<?php

namespace App\Filament\Resources\PermintaanPenjemputans\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PermintaanPenjemputanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            // 🔹 User
            Select::make('user_id')
                ->label('User')
                ->relationship('user', 'name')
                ->required(),

            // 🔹 Jenis Sampah
            Select::make('id_informasi_sampah')
                ->label('Jenis Sampah')
                ->relationship('jenisSampah', 'jenis_sampah') // pastikan kolom di tabel jenis_sampah sesuai
                ->required(),

            // 🔹 Berat (perkiraan)
            TextInput::make('berat')
                ->label('Perkiraan Berat (kg)')
                ->numeric()
                ->required(),

            // 🔹 Nomor HP
            TextInput::make('no_hp')
                ->label('Nomor HP')
                ->tel()
                ->required(),

            // 🔹 Lokasi
            TextInput::make('latitude')
                ->label('Latitude')
                ->numeric()
                ->required(),

            TextInput::make('longitude')
                ->label('Longitude')
                ->numeric()
                ->required(),

            // 🔹 Keterangan tambahan
            Textarea::make('keterangan')
                ->label('Keterangan')
                ->rows(3)
                ->nullable(),

            // 🔹 Status (default: menunggu)
            Select::make('status')
                ->label('Status')
                ->options([
                    'menunggu' => 'Menunggu',
                    'diproses' => 'Diproses',
                    'dijemput' => 'Dijemput',
                    'selesai' => 'Selesai',
                    'dibatalkan' => 'Dibatalkan',
                ])
                ->default('menunggu'),
        ]);
    }
}
