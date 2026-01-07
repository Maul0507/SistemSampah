<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InformasiSampah; // Pastikan Model ini ada
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class InformasiSampahController extends Controller
{
    /**
     * 1. GET: Menampilkan semua jenis sampah
     */
    public function index()
    {
        // DIGANTI: 'nama_sampah' -> 'jenis_sampah'
        $sampah = InformasiSampah::orderBy('jenis_sampah', 'asc')->get();
        return response()->json([
            'success' => true,
            'message' => 'Daftar informasi sampah berhasil diambil',
            'data' => $sampah,
        ], 200);
    }

    /**
     * 2. POST: Menyimpan jenis sampah baru
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // DIGANTI:
            'jenis_sampah' => 'required|string|max:255|unique:informasi_sampah',
            'harga' => 'required|integer',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $gambarUrl = null;
        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('sampah-images', 'public');
            $gambarUrl = Storage::url($path);
        }

        $sampah = InformasiSampah::create([
            // DIGANTI:
            'jenis_sampah' => $request->jenis_sampah,
            'harga' => $request->harga,
            'gambar' => $gambarUrl, // Sesuaikan dengan nama kolom migrasi Anda
            'deskripsi' => $request->deskripsi, // (Jika Anda mengirim ini dari Flutter)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Jenis sampah baru berhasil ditambahkan!',
            'data' => $sampah,
        ], 201);
    }

    /**
     * 3. POST (Update): Mengupdate jenis sampah
     */
    public function update(Request $request, $id)
    {
        $sampah = InformasiSampah::find($id);
        if (!$sampah) {
            return response()->json(['success' => false, 'message' => 'Data sampah tidak ditemukan'], 404);
        }
        
        $validator = Validator::make($request->all(), [
            // DIGANTI:
            'jenis_sampah' => 'required|string|max:255|unique:informasi_sampah,jenis_sampah,' . $id,
            'harga' => 'required|integer',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // DIGANTI: 'gambar'
        $gambarUrl = $sampah->gambar; // Gunakan gambar lama by default

        if ($request->hasFile('gambar')) {
            // Hapus gambar lama
            if ($sampah->gambar) {
                $oldPath = str_replace(Storage::url(''), '', $sampah->gambar);
                Storage::disk('public')->delete($oldPath);
            }
            // Simpan gambar baru
            $path = $request->file('gambar')->store('sampah-images', 'public');
            $gambarUrl = Storage::url($path);
        }

        $sampah->update([
            // DIGANTI:
            'jenis_sampah' => $request->jenis_sampah,
            'harga' => $request->harga,
            'gambar' => $gambarUrl,
            'deskripsi' => $request->deskripsi, // (Jika Anda mengirim ini)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data sampah berhasil diupdate!',
            'data' => $sampah,
        ], 200);
    }

    /**
     * 4. DELETE: Menghapus jenis sampah
     */
    public function destroy($id)
    {
        $sampah = InformasiSampah::find($id);
        if (!$sampah) {
            return response()->json(['success' => false, 'message' => 'Data sampah tidak ditemukan'], 404);
        }

        // DIGANTI: 'gambar'
        if ($sampah->gambar) {
            $oldPath = str_replace(Storage::url(''), '', $sampah->gambar);
            Storage::disk('public')->delete($oldPath);
        }

        $sampah->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data sampah berhasil dihapus!',
        ], 200);
    }
}