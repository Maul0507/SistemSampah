<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InformasiSampah extends Model
{
    use HasFactory;

    protected $table = 'informasi_sampah'; // Pastikan sesuai nama tabel di migration
    protected $fillable = [
        'jenis_sampah',
        'harga',
        'gambar',
        'deskripsi',
    ];
    public function permintaanPenjemputan()
    {
        return $this->hasMany(PermintaanPenjemputan::class, 'id_jenis_sampah');
    }
}
