<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PenukaranSaldo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PenukaranSaldoController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'amount' => 'required|integer|min:1000',
            'bank_name' => 'required|string|max:100',
            'account_number' => 'required|string|max:50',
            'account_name' => 'required|string|max:100',
        ]);

        if ($user->saldo < $validated['amount']) {
            return response()->json([
                'success' => false,
                'message' => 'Saldo tidak mencukupi'
            ], 400);
        }

        $penukaran = PenukaranSaldo::create([
            'user_id' => $user->id,
            'amount' => $validated['amount'],
            'bank_name' => $validated['bank_name'],
            'account_number' => $validated['account_number'],
            'account_name' => $validated['account_name'],
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'data' => $penukaran
        ], 201);
    }

    public function myHistory()
    {
        return response()->json([
            'success' => true,
            'data' => PenukaranSaldo::where('user_id', Auth::id())
                ->latest()
                ->get()
        ]);
    }
}
