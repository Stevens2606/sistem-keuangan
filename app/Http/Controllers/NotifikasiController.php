<?php

namespace App\Http\Controllers;

use App\Services\AnggaranNotifikasiService;

class NotifikasiController extends Controller
{
    /**
     * Endpoint JSON — dipanggil via fetch() saat halaman dimuat,
     * untuk mengisi bell icon dengan notifikasi yang sudah aktif
     * sebelum ada event WebSocket baru.
     */
    public function anggaranAktif()
    {
        $notifikasi = AnggaranNotifikasiService::aktif();

        return response()->json([
            'count' => $notifikasi->count(),
            'data'  => $notifikasi,
        ]);
    }
}