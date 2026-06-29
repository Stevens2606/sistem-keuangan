<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
*/

// Hanya admin & bendahara yang boleh mendengarkan notifikasi anggaran.
// Sesuaikan 'role' di bawah kalau nama kolom/method role kamu berbeda
// (misal kalau pakai $user->role->nama, ganti jadi: in_array($user->role->nama, [...]))
Broadcast::channel('notifikasi.keuangan', function ($user) {
    return in_array($user->role, ['admin', 'bendahara']);
});