<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthApiController extends Controller
{
    /**
     * Login mobile — beda mekanisme dari login web.
     * Web pakai session cookie (Auth::attempt + redirect).
     * Mobile/API pakai token: validasi kredensial manual, lalu generate token Sanctum
     * yang harus disimpan di sisi aplikasi mobile dan dikirim di setiap request berikutnya
     * lewat header "Authorization: Bearer {token}".
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validasi gagal.',
                'data'    => $validator->errors(),
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Email atau password salah.',
                'data'    => null,
            ], 401);
        }

        // Cek juga status aktif user (kolom is_active sudah ada di model User),
        // supaya user yang dinonaktifkan admin tidak bisa login lewat mobile juga.
        if (! $user->is_active) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Akun Anda tidak aktif. Hubungi admin.',
                'data'    => null,
            ], 403);
        }

        // Hapus token lama dengan nama yang sama supaya tidak menumpuk token mati
        // setiap kali user login ulang dari device yang sama.
        $user->tokens()->where('name', 'mobile-token')->delete();

        $token = $user->createToken('mobile-token')->plainTextToken;

        return response()->json([
            'status'  => 'success',
            'message' => 'Login berhasil.',
            'data'    => [
                'user'  => [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'email' => $user->email,
                    'role'  => $user->role,
                ],
                'token' => $token,
            ],
        ], 200);
    }

    /**
     * Logout — hapus token yang sedang dipakai request ini saja
     * (kalau user login dari banyak device, device lain tetap login).
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Logout berhasil.',
            'data'    => null,
        ], 200);
    }

    /**
     * Profil user yang sedang login (dicocokkan dari token yang dikirim).
     */
    public function user(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'status'  => 'success',
            'message' => 'Berhasil mengambil data profil.',
            'data'    => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $user->role,
            ],
        ], 200);
    }
}