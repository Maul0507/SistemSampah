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
        'id_informasi_sampah', // (Opsional) jika masih dipakai untuk sampah utama
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
    // (Ini relasi lama Anda, tetap dibiarkan jika masih dipakai)
    public function jenisSampah()
    {
        return $this->belongsTo(InformasiSampah::class, 'id_informasi_sampah');
    }

    // 3. RELASI DETAIL SAMPAH (Many-to-Many)
    // 🔥 INI YANG PERLU DITAMBAHKAN AGAR ERROR HILANG 🔥
    public function detailSampah()
    {
        return $this->belongsToMany(
            InformasiSampah::class,      // Model tujuan
            'detail_permintaan',         // Nama tabel pivot (Sesuai migration kamu)
            'permintaan_penjemputan_id', // Foreign Key di tabel pivot untuk model ini
            'informasi_sampah_id'        // Foreign Key di tabel pivot untuk model tujuan
        )
        ->withPivot('estimasi_berat_kg') // Agar kolom berat di tabel pivot bisa dibaca/disimpan
        ->withTimestamps();              // Agar created_at & updated_at di pivot terisi otomatis
    }
}