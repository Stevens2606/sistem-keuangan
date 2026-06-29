<?php

namespace App\Services;

use App\Models\Anggaran;
use App\Models\Transaksi;

class AnggaranNotifikasiService
{
    /**
     * Ambil semua notifikasi anggaran aktif (mendekati/melebihi batas) bulan ini.
     * Dipakai oleh Dashboard dan Bell Icon (real-time).
     */
    public static function aktif()
    {
        $bulanIni = now()->month;
        $tahunIni = now()->year;

        return Anggaran::with('kategori')
            ->where('periode_bulan', $bulanIni)
            ->where('periode_tahun', $tahunIni)
            ->get()
            ->map(function ($anggaran) use ($bulanIni, $tahunIni) {
                // Hanya transaksi yang SUDAH DISETUJUI yang dihitung sebagai realisasi anggaran.
                // Transaksi yang masih menunggu persetujuan belum benar-benar mengeluarkan dana.
                $realisasi = Transaksi::approved()
                    ->where('kategori_id', $anggaran->kategori_id)
                    ->where('tipe', 'keluar')
                    ->whereMonth('tanggal', $bulanIni)
                    ->whereYear('tanggal', $tahunIni)
                    ->sum('jumlah');

                $persentase = $anggaran->jumlah > 0
                    ? round(($realisasi / $anggaran->jumlah) * 100)
                    : 0;

                return [
                    'kategori'   => $anggaran->kategori->nama,
                    'anggaran'   => $anggaran->jumlah,
                    'realisasi'  => $realisasi,
                    'persentase' => $persentase,
                    'melebihi'   => $realisasi > $anggaran->jumlah,
                    'mendekati'  => $persentase >= 80 && $realisasi <= $anggaran->jumlah,
                ];
            })
            ->filter(fn ($item) => $item['melebihi'] || $item['mendekati'])
            ->values();
    }

    /**
     * Cek satu kategori tertentu (dipanggil setelah transaksi disetujui/diedit).
     * Return array data notifikasi kalau ambang batas tercapai, null kalau tidak.
     */
    public static function cekSatuKategori(int $kategoriId): ?array
    {
        $bulanIni = now()->month;
        $tahunIni = now()->year;

        $anggaran = Anggaran::with('kategori')
            ->where('kategori_id', $kategoriId)
            ->where('periode_bulan', $bulanIni)
            ->where('periode_tahun', $tahunIni)
            ->first();

        if (!$anggaran) {
            return null;
        }

        // Hanya transaksi yang SUDAH DISETUJUI yang dihitung sebagai realisasi anggaran.
        $realisasi = Transaksi::approved()
            ->where('kategori_id', $kategoriId)
            ->where('tipe', 'keluar')
            ->whereMonth('tanggal', $bulanIni)
            ->whereYear('tanggal', $tahunIni)
            ->sum('jumlah');

        $persentase = $anggaran->jumlah > 0
            ? round(($realisasi / $anggaran->jumlah) * 100)
            : 0;

        if ($persentase < 80) {
            return null;
        }

        return [
            'kategori'   => $anggaran->kategori->nama,
            'anggaran'   => $anggaran->jumlah,
            'realisasi'  => $realisasi,
            'persentase' => $persentase,
            'melebihi'   => $realisasi > $anggaran->jumlah,
            'waktu'      => now()->format('H:i:s'),
        ];
    }
}