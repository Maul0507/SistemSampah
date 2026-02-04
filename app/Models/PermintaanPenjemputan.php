<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermintaanPenjemputan extends Model
{
    use HasFactory;

    // ✅ Nama tabel utama
    protected $table = 'permintaan_penjemputan';

    // ✅ Mass assignment
    protected $fillable = [
        'user_id',
        'id_informasi_sampah',
        'no_hp',
        'berat',
        'latitude',
        'longitude',
        'keterangan',
        'status',
    ];

    // ==========================================
    // RELASI
    // ==========================================

    // 1. Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 2. Relasi ke Jenis Sampah Utama (Single - BelongsTo)
    public function jenisSampah()
    {
        return $this->belongsTo(InformasiSampah::class, 'id_informasi_sampah');
    }

    // 3. RELASI DETAIL SAMPAH (Many-to-Many)
    public function detailSampah()
    {
        return $this->belongsToMany(
            InformasiSampah::class,
            'detail_permintaan',
            'permintaan_penjemputan_id',
            'informasi_sampah_id'
        )
        ->withPivot('estimasi_berat_kg')
        ->withTimestamps();
    }
}