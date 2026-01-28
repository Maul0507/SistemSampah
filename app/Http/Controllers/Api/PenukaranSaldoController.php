<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PenukaranSaldo;
use App\Models\User; // Pastikan import User agar bisa update saldo
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // Untuk transaksi database yang aman

class PenukaranSaldoController extends Controller
{
    // 1. ADMIN: MELIHAT SEMUA REQUEST
    // Route: GET /api/penukaran-saldo
    public function index()
    {
        // Ambil semua data urut dari terbaru
        // with('user') opsional: jika ingin menampilkan nama user di admin panel
        $data = PenukaranSaldo::latest()->get();

        return response()->json([
            'success' => true,
            'message' => 'List semua penukaran',
            'data'    => $data
        ]);
    }

    // 2. USER: MELIHAT RIWAYAT SENDIRI
    // Route: GET /api/penukaran-saldo/me
    public function myHistory()
    {
        return response()->json([
            'success' => true,
            'data'    => PenukaranSaldo::where('user_id', Auth::id())
                ->latest()
                ->get()
        ]);
    }

    // 3. USER: MENGAJUKAN PENARIKAN (STORE)
    // Route: POST /api/penukaran-saldo
    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|integer|min:10000', // Minimal 10rb misal
            'bank_name' => 'required|string|max:100',
            'account_number' => 'required|string|max:50',
            'account_name' => 'required|string|max:100',
        ]);

        $user = $request->user();

        // Cek Saldo
        if ($user->saldo < $request->amount) {
            return response()->json([
                'success' => false,
                'message' => 'Saldo tidak mencukupi'
            ], 400);
        }

        // --- MULAI TRANSAKSI DATABASE (Agar aman) ---
        DB::beginTransaction();
        try {
            // 1. Potong Saldo User SEKARANG
            $user->saldo = $user->saldo - $request->amount;
            $user->save();

            // 2. Buat Record
            $penukaran = PenukaranSaldo::create([
                'user_id' => $user->id,
                'amount' => $request->amount,
                'bank_name' => $request->bank_name,
                'account_number' => $request->account_number,
                'account_name' => $request->account_name,
                'status' => 'pending',
            ]);

            DB::commit(); // Simpan perubahan

            return response()->json([
                'success' => true,
                'message' => 'Permintaan berhasil dikirim',
                'data' => [
                    'withdrawal' => $penukaran,
                    'remaining_balance' => $user->saldo // Penting untuk update UI Flutter
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollback(); // Batalkan jika ada error
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses: ' . $e->getMessage()
            ], 500);
        }
    }

    // 4. ADMIN & USER: UPDATE STATUS (Acc / Tolak / Batal)
    // Route: PUT /api/penukaran-saldo/{id}
    public function updateStatus(Request $request, $id)
    {
        $penukaran = PenukaranSaldo::find($id);

        if (!$penukaran) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        }

        // Validasi input status
        $request->validate([
            'status' => 'required|in:success,rejected,cancelled'
        ]);

        $newStatus = $request->status;

        DB::beginTransaction();
        try {
            // LOGIKA PENGEMBALIAN DANA (REFUND)
            // Jika Admin menolak (rejected) ATAU User membatalkan (cancelled)
            // DAN status sebelumnya masih 'pending', maka saldo dikembalikan.
            if (($newStatus == 'rejected' || $newStatus == 'cancelled') && $penukaran->status == 'pending') {
                $user = User::find($penukaran->user_id);
                if ($user) {
                    $user->saldo = $user->saldo + $penukaran->amount;
                    $user->save();
                }
            }

            // Update status penukaran
            $penukaran->status = $newStatus;
            $penukaran->save();

            DB::commit();

            // Ambil saldo user terbaru untuk response
            $currentUserBalance = 0;
            $userCheck = User::find($penukaran->user_id);
            if($userCheck) {
                $currentUserBalance = $userCheck->saldo;
            }

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diperbarui',
                'data' => $penukaran,
                'current_balance' => $currentUserBalance // Untuk update UI Flutter
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal update: ' . $e->getMessage()
            ], 500);
        }
    }
}