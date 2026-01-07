<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PermintaanPenjemputan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function stats()
    {
        // 1. Jumlah Permintaan Hari Ini
        // Menghitung semua permintaan yang dibuat hari ini (created_at = today)
        $permintaanHariIni = PermintaanPenjemputan::whereDate('created_at', Carbon::today())->count();

        // 2. Jumlah Sampah Terjual Bulan Ini (Kg)
        // Menjumlahkan kolom 'berat' dari permintaan yang statusnya 'selesai'
        // Kita hitung untuk bulan ini saja agar relevan
        $sampahTerjualBulanIni = PermintaanPenjemputan::where('status', 'selesai')
            ->whereMonth('updated_at', Carbon::now()->month)
            ->whereYear('updated_at', Carbon::now()->year)
            ->sum('berat');

        // 3. Jumlah User Aktif (Total Nasabah)
        // Menghitung user dengan role 'nasabah'
        $userAktif = User::where('role', 'nasabah')->count();

        // 4. Data Grafik Mingguan (7 Hari Terakhir)
        // Loop 7 hari ke belakang untuk menghitung jumlah permintaan per hari
        $grafikMingguan = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            
            // Hitung jumlah permintaan pada tanggal tersebut
            $count = PermintaanPenjemputan::whereDate('created_at', $date)->count();
            
            // Format nama hari (Sen, Sel, Rab...) dalam bahasa Indonesia
            // Pastikan locale ID sudah diatur di config/app.php atau gunakan isoFormat
            $dayName = $date->locale('id')->isoFormat('ddd'); 
            
            $grafikMingguan[] = [
                'day' => $dayName,
                'count' => $count
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'permintaan_hari_ini' => $permintaanHariIni,
                'sampah_terjual_kg' => (float) $sampahTerjualBulanIni, // Cast ke float agar aman
                'user_aktif' => $userAktif,
                'grafik_mingguan' => $grafikMingguan
            ]
        ], 200);
    }
}