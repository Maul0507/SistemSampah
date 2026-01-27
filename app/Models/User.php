<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',     // Pastikan ini ada jika Anda menggunakan role
        'no_hp',    // Pastikan ini ada jika Anda menggunakan no_hp
        'alamat',   // Pastikan ini ada jika Anda menggunakan alamat
        'saldo',    // <-- TAMBAHAN PENTING: Kolom Saldo
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'saldo' => 'decimal:2', 
        ];
    }

    /**
     * Relasi ke Permintaan Penjemputan (One to Many)
     */
    public function permintaanPenjemputan()
    {
        return $this->hasMany(PermintaanPenjemputan::class, 'user_id');
    }

    /**
     * Relasi ke Transaksi (One to Many)
     * User memiliki banyak transaksi (pemasukan/pengeluaran saldo)
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'user_id');
    }
}