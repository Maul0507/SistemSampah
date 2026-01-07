<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            // Relasi ke user (nasabah)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Jenis transaksi: 'credit' (masuk/jual sampah) atau 'debit' (tarik saldo)
            $table->enum('type', ['credit', 'debit']);
            
            // Jumlah uang
            $table->decimal('amount', 15, 2);
            
            // Keterangan (misal: "Penjualan Sampah ID #123" atau "Penarikan Saldo")
            $table->string('description')->nullable();
            
            // Status (opsional, berguna untuk penarikan saldo yang butuh persetujuan)
            $table->enum('status', ['pending', 'success', 'failed'])->default('success');
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};