<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('permintaan_penjemputan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('id_informasi_sampah')->constrained('informasi_sampah')->cascadeOnDelete();
            $table->decimal('berat', 8, 2);
            $table->string('no_hp');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->text('keterangan')->nullable();
            $table->enum('status', ['menunggu', 'diproses', 'dijemput', 'selesai', 'dibatalkan'])->default('menunggu');
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('permintaan_penjemputan');
    }
};

