<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    // Mengizinkan mass assignment untuk semua kolom kecuali 'id'
    protected $guarded = ['id'];

    /**
     * Relasi: Setiap transaksi dimiliki oleh satu User (Nasabah).
     * Ini mempermudah kita mengakses data user dari transaksi,
     * contoh: $transaction->user->name
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}