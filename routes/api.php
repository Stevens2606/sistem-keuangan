<?php

use App\Http\Controllers\Api\AuthApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Semua route di sini otomatis diberi prefix /api dan middleware group 'api'
| (lihat bootstrap/app.php). Dipakai oleh aplikasi mobile, terpisah dari
| routes/web.php yang dipakai browser (session-based).
|
| Autentikasi pakai Laravel Sanctum (token-based), BUKAN session cookie.
|
*/

// Endpoint publik — tidak perlu token, dipakai untuk mendapatkan token pertama kali.
Route::post('/login', [AuthApiController::class, 'login']);

// Endpoint yang butuh token Sanctum (kirim header: Authorization: Bearer {token})
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthApiController::class, 'logout']);
    Route::get('/user', [AuthApiController::class, 'user']);
});