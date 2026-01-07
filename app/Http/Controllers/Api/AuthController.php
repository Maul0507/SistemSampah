<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Registrasi user baru
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            // 'no_hp'    => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'nasabah', // default role nasabah
            // 'no_hp'    => $request->no_hp,
            'saldo'    => 0,         // saldo awal 0
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success'     => true,
            'message'     => 'Registrasi berhasil!',
            'access_token'=> $token,
            'token_type'  => 'Bearer',
            'user'        => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $user->role,
                // 'no_hp' => $user->no_hp,
                'saldo' => $user->saldo,              // <-- SALDO DIKIRIM KE FLUTTER
            ]
        ], 201);
    }

    /**
     * Login user
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah'
            ], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success'     => true,
            'message'     => 'Login berhasil!',
            'access_token'=> $token,
            'token_type'  => 'Bearer',
            'user'        => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $user->role,
                // 'no_hp' => $user->no_hp,
                'saldo' => $user->saldo,              // <-- SALDO DIKIRIM KE FLUTTER
            ]
        ]);
    }

    /**
     * Logout user (hapus token saat ini)
     */
    public function logout(Request $request)
    {
        if ($request->user()) {
            $request->user()->currentAccessToken()->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil!'
        ]);
    }

    /**
     * Get all users (untuk admin/petugas)
     */
    public function index()
    {
        $users = User::orderBy('name', 'asc')->get();

        return response()->json([
            'success' => true,
            'data'    => $users->makeHidden(['password', 'email_verified_at', 'remember_token'])
        ], 200);
    }

    /**
     * Tambah user oleh admin/petugas
     */
    public function storeUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role'     => 'required|string|in:admin,petugas,nasabah',
            // 'no_hp'    => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
            // 'no_hp'    => $request->no_hp,
            'saldo'    => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User berhasil ditambahkan',
            'data'    => $user
        ], 201);
    }

    public function me(Request $request)
{
    $user = $request->user();

    return response()->json([
        'success' => true,
        'data' => $user->makeHidden([
            'password',
            'email_verified_at',
            'remember_token'
        ])
    ], 200);
}

    /**
     * Update user oleh admin/petugas
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email,' . $id,
            'role'     => 'required|string|in:admin,petugas,nasabah',
            // 'no_hp'    => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $user->update([
            'name'     => $request->name,
            'email'    => $request->email,
            'role'     => $request->role,
            // 'no_hp'    => $request->no_hp,
            'password' => $request->filled('password') ? Hash::make($request->password) : $user->password,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User berhasil diupdate',
            'data'    => $user
        ]);
    }

    /**
     * Hapus user oleh admin
     */
    public function destroyUser($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User berhasil dihapus'
        ]);
    }
}