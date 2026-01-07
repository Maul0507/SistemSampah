<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('detail_permintaan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permintaan_penjemputan_id')->constrained('permintaan_penjemputan')->cascadeOnDelete();
            $table->foreignId('informasi_sampah_id')->constrained('informasi_sampah')->cascadeOnDelete();
            $table->decimal('estimasi_berat_kg', 8, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_permintaan');
    }
};
