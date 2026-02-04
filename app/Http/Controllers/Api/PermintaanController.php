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
        // 1. Validasi
        $validator = Validator::make($request->all(), [
            'no_hp' => 'required|string|max:15',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'keterangan' => 'nullable|string',
            'berat' => 'nullable|numeric',
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

        // 2. Auth check
        $userId = Auth::id();
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        // 3. Hitung total berat jika tidak dikirim
        $totalBerat = $request->berat ?? collect($request->detail_sampah)->sum('estimasi_berat_kg');

        // 4. Simpan permintaan (id_informasi_sampah = NULL)
        $permintaan = PermintaanPenjemputan::create([
            'user_id' => $userId,
            'no_hp' => $request->no_hp,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'keterangan' => $request->keterangan,
            'status' => 'menunggu',
            'berat' => $totalBerat,
            'id_informasi_sampah' => null,
        ]);

        // 5. Simpan SEMUA detail sampah ke pivot table
        foreach ($request->detail_sampah as $sampah) {
            $permintaan->detailSampah()->attach(
                $sampah['informasi_sampah_id'],
                ['estimasi_berat_kg' => $sampah['estimasi_berat_kg']]
            );
        }

        // 6. Load relasi untuk response
        $permintaan->load('detailSampah', 'user');

        return response()->json([
            'success' => true,
            'message' => 'Permintaan penjemputan berhasil dibuat',
            'data' => $permintaan,
        ], 201);
    }

    /**
     * Riwayat permintaan user
     */
    public function riwayatUser()
    {
        try {
            $user = Auth::user();

            $riwayat = PermintaanPenjemputan::where('user_id', $user->id)
                ->with('detailSampah') // Load detail sampah
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Daftar riwayat penjemputan',
                'data' => $riwayat
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
     * Daftar tugas masuk (permintaan masuk)
     * ✅ DIPERBAIKI: Tambah with('detailSampah')
     */
    public function tugasMasuk()
    {
        $tugas = PermintaanPenjemputan::with(['user', 'detailSampah']) // ✅ TAMBAH INI
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
     * ✅ DIPERBAIKI: Tambah with('detailSampah')
     */
    public function tugasAktif()
    {
        $tugas = PermintaanPenjemputan::with(['user', 'detailSampah']) // ✅ TAMBAH INI
            ->whereIn('status', ['menunggu', 'diproses', 'dijemput'])
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $tugas,
        ]);
    }

    /**
     * Riwayat tugas petugas
     * ✅ DIPERBAIKI: Tambah with('detailSampah')
     */
    public function riwayatPetugas()
    {
        $riwayat = PermintaanPenjemputan::with(['user', 'detailSampah']) // ✅ TAMBAH INI
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
     * ✅ DIPERBAIKI: Load detailSampah di response
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

        // Driver update berat asli
        if ($request->filled('berat_riil')) {
            $permintaan->berat = $request->berat_riil;
        }

        if ($request->status === 'dibatalkan' && $request->filled('alasan_pembatalan')) {
            $permintaan->keterangan .= ' [DIBATALKAN: ' . $request->alasan_pembatalan . ']';
        }

        $permintaan->save();
        $permintaan->load(['user', 'detailSampah']); // ✅ TAMBAH detailSampah

        return response()->json([
            'success' => true,
            'message' => 'Status berhasil diperbarui',
            'data' => $permintaan,
        ]);
    }

    /**
     * Selesaikan tugas
     * ✅ DIPERBAIKI: Load detailSampah di response
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
        $permintaan->load(['user', 'detailSampah']); // ✅ TAMBAH INI

        return response()->json([
            'success' => true,
            'message' => 'Tugas berhasil diselesaikan',
            'data' => $permintaan // ✅ TAMBAH data
        ]);
    }
}