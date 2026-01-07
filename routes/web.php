<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Route login sederhana agar Filament tidak error
Route::get('/login', function () {
    return response()->json(['message' => 'Login route placeholder. Use /api/login instead.']);
})->name('login');

Route::get('/logout', function () {
    return response()->json(['message' => 'Logout route placeholder. Use /api/logout instead.']);
})->name('logout');
