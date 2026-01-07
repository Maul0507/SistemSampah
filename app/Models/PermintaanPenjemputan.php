<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermintaanPenjemputan extends Model
{
    use HasFactory;

    // ✅ Pastikan tabelnya sesuai
    protected $table = 'permintaan_penjemputan';

    // ✅ Mass assignment (jika kamu pakai create atau update)
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

    // ✅ Relasi ke pengguna (user)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ✅ Relasi ke jenis sampah
    public function jenisSampah()
    {
        return $this->belongsTo(InformasiSampah::class, 'id_informasi_sampah');
    }

    
}
