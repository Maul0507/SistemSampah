<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PermintaanPenjemputan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PermintaanController extends Controller
{
    // ==========================================
    // BAGIAN 1: UNTUK USER (MASYARAKAT / NASABAH)
    // ==========================================

    /**
     * Membuat permintaan penjemputan baru
     */
    public function store(Request $request)
    {
        // 1. Validasi input
        $validator = Validator::make($request->all(), [
            'no_hp' => 'required|string|max:15',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'keterangan' => 'nullable|string',

            // BERAT BOLEH TIDAK DIISI
            'berat' => 'nullable|numeric',

            // Detail sampah
            'detail_sampah' => 'required|array|min:1',
            'detail_sampah.*.informasi_sampah_id' => 'required|integer|exists:informasi_sampah,id',
            'detail_sampah.*.estimasi_berat_kg' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        // 2. Ambil user login
        $userId = Auth::id();
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        // 3. Ambil sampah pertama (wajib karena kolom utama butuh id)
        $sampahUtama = $request->detail_sampah[0];

        // 4. SIMPAN PERMINTAAN (FIX ERROR BERAT)
        $permintaan = PermintaanPenjemputan::create([
            'user_id' => $userId,
            'no_hp' => $request->no_hp,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'keterangan' => $request->keterangan,
            'status' => 'menunggu',

            // 🔥 FIX UTAMA
            'berat' => $request->berat ?? $sampahUtama['estimasi_berat_kg'] ?? 0,

            'id_informasi_sampah' => $sampahUtama['informasi_sampah_id'],
        ]);

        // 5. Simpan detail sampah (pivot)
        foreach ($request->detail_sampah as $sampah) {
            try {
                $permintaan->detailSampah()->attach(
                    $sampah['informasi_sampah_id'],
                    ['estimasi_berat_kg' => $sampah['estimasi_berat_kg']]
                );
            } catch (\Exception $e) {
                // Abaikan jika pivot tidak digunakan
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Permintaan penjemputan berhasil dibuat',
            'data' => $permintaan,
        ], 201);
    }

    /**
     * Riwayat permintaan user
     */
    /**
     * Riwayat permintaan user
     */
    public function riwayatUser()
{
    try {
        $user = Auth::user();

        $riwayat = PermintaanPenjemputan::where('user_id', $user->id)
            ->with('detailSampah') // ✅ CUKUP INI
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar riwayat penjemputan',
            'data'    => $riwayat
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
        ], 500);
    }
}


    // ==========================================
    // BAGIAN 2: UNTUK PETUGAS (DRIVER)
    // ==========================================

    /**
     * Daftar tugas masuk
     */
    public function tugasMasuk()
    {
        $tugas = PermintaanPenjemputan::with('user')
            ->where('status', 'menunggu')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $tugas,
        ]);
    }

    /**
     * Tugas aktif (untuk peta)
     */
    public function tugasAktif()
    {
        $tugas = PermintaanPenjemputan::whereIn('status', ['menunggu', 'diproses', 'dijemput'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $tugas,
        ]);
    }

    /**
     * Riwayat tugas petugas
     */
    public function riwayatPetugas()
    {
        $riwayat = PermintaanPenjemputan::with('user')
            ->whereIn('status', ['selesai', 'dibatalkan'])
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $riwayat,
        ]);
    }

    /**
     * Update status oleh driver
     */
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:menunggu,diproses,dijemput,selesai,dibatalkan',
            'alasan_pembatalan' => 'nullable|string',
            'berat_riil' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $permintaan = PermintaanPenjemputan::find($id);
        if (!$permintaan) {
            return response()->json([
                'success' => false,
                'message' => 'Permintaan tidak ditemukan'
            ], 404);
        }

        $permintaan->status = $request->status;

        // 🔥 DRIVER UPDATE BERAT ASLI
        if ($request->filled('berat_riil')) {
            $permintaan->berat = $request->berat_riil;
        }

        if ($request->status === 'dibatalkan' && $request->filled('alasan_pembatalan')) {
            $permintaan->keterangan .= ' [DIBATALKAN: ' . $request->alasan_pembatalan . ']';
        }

        $permintaan->save();
        $permintaan->load('user');

        return response()->json([
            'success' => true,
            'message' => 'Status berhasil diperbarui',
            'data' => $permintaan,
        ]);
    }

    /**
     * Selesaikan tugas
     */
    public function selesaikanTugas($id)
    {
        $permintaan = PermintaanPenjemputan::find($id);

        if (!$permintaan) {
            return response()->json([
                'success' => false,
                'message' => 'Permintaan tidak ditemukan'
            ], 404);
        }

        if ($permintaan->status === 'selesai') {
            return response()->json([
                'success' => false,
                'message' => 'Tugas sudah selesai'
            ], 409);
        }

        $permintaan->status = 'selesai';
        $permintaan->save();

        return response()->json([
            'success' => true,
            'message' => 'Tugas berhasil diselesaikan'
        ]);
    }
}
