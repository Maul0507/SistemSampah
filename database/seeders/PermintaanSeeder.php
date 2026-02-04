<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PermintaanPenjemputan;
use App\Models\User;

class PermintaanSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil ID User pertama sebagai penanggung jawab (pastikan user sudah ada)
        $userId = User::first()?->id ?? 1;

        $dataPermintaan = [
            [
                'user_id' => $userId,
                'no_hp' => '081234567890',
                'latitude' => '-6.200000',
                'longitude' => '106.816666',
                'keterangan' => 'Penjemputan sampah plastik di depan gerbang',
                'status' => 'menunggu',
                'berat' => 5.5,
                'id_informasi_sampah' => null,
            ],
            [
                'user_id' => $userId,
                'no_hp' => '089987654321',
                'latitude' => '-6.175110',
                'longitude' => '106.865039',
                'keterangan' => 'Tolong ambil kardus bekas pindahan',
                'status' => 'menunggu',
                'berat' => 12.0,
                'id_informasi_sampah' => null,
            ],
        ];

        foreach ($dataPermintaan as $data) {
            PermintaanPenjemputan::create($data);
        }
    }
}