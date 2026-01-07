<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User; // <-- PENTING: Import Model User
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB; // <-- PENTING: Untuk Transaksi Database

class TransactionController extends Controller
{
    /**
     * GET: Saldo User Saat Ini
     * Mengambil data langsung dari kolom saldo di tabel users.
     */
    public function balance(Request $request)
    {
        // Ambil ID user dari token (jika login) atau request (jika testing)
        $userId = Auth::guard('sanctum')->id() ?? $request->user_id;

        if (!$userId) {
             return response()->json(['message' => 'User ID tidak ditemukan'], 400);
        }

        // Ambil user untuk melihat saldo aktual di tabel users
        $user = User::find($userId);
        
        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }

        // Kita juga hitung total masuk/keluar untuk sekadar info statistik
        $totalIncome = Transaction::where('user_id', $userId)
            ->where('type', 'credit')
            ->where('status', 'success')
            ->sum('amount');
            
        $totalWithdrawn = Transaction::where('user_id', $userId)
            ->where('type', 'debit')
            ->where('status', 'success')
            ->sum('amount');

        return response()->json([
            'success' => true,
            'data' => [
                'balance' => $user->saldo, // <-- Mengambil langsung dari kolom saldo user
                'total_income' => $totalIncome,
                'total_withdrawn' => $totalWithdrawn,
            ]
        ], 200);
    }

    /**
     * GET: Riwayat Transaksi
     */
    public function history(Request $request)
    {
        $userId = Auth::guard('sanctum')->id() ?? $request->user_id;

        if (!$userId) {
             return response()->json(['message' => 'User ID tidak ditemukan'], 400);
        }

        $transactions = Transaction::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $transactions,
        ], 200);
    }

    /**
     * POST: Tambah Transaksi Baru & Update Saldo
     * Digunakan saat Driver menyelesaikan tugas (tipe 'credit')
     * atau saat User melakukan penarikan (tipe 'debit').
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:credit,debit',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'status' => 'nullable|in:pending,success,failed',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Mulai Transaksi Database (Atomic Operation)
        // Ini memastikan jika salah satu gagal, semua dibatalkan.
        DB::beginTransaction();

        try {
            // 1. Buat Catatan Transaksi di tabel 'transactions'
            $transaction = Transaction::create([
                'user_id' => $request->user_id,
                'type' => $request->type,
                'amount' => $request->amount,
                'description' => $request->description,
                'status' => $request->status ?? 'success',
            ]);

            // 2. Update Saldo User di tabel 'users'
            // lockForUpdate() mencegah race condition (jika ada banyak request bersamaan)
            $user = User::lockForUpdate()->find($request->user_id); 

            if ($request->type == 'credit') {
                // Uang Masuk (Jual Sampah) -> Tambah Saldo
                $user->saldo += $request->amount;
            } else {
                // Uang Keluar (Penarikan) -> Kurangi Saldo
                // Cek apakah saldo cukup
                if ($user->saldo < $request->amount) {
                    DB::rollBack(); // Batalkan semuanya jika saldo kurang
                    return response()->json([
                        'success' => false, 
                        'message' => 'Saldo tidak mencukupi untuk penarikan ini.'
                    ], 400);
                }
                $user->saldo -= $request->amount;
            }

            $user->save(); // Simpan perubahan saldo ke tabel users

            // Jika semua berhasil, Commit ke database
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dicatat & saldo diperbarui',
                'data' => $transaction,
                'current_balance' => $user->saldo
            ], 201);

        } catch (\Exception $e) {
            // Jika ada error apa pun, batalkan semua perubahan database
            DB::rollBack();
            return response()->json([
                'success' => false, 
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
            ], 500);
        }
    }
}