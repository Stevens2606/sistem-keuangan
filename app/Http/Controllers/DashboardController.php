<?php
namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Anggaran;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Total keseluruhan
        $totalMasuk  = Transaksi::where('tipe', 'masuk')->sum('jumlah');
        $totalKeluar = Transaksi::where('tipe', 'keluar')->sum('jumlah');
        $saldo       = $totalMasuk - $totalKeluar;

        // Transaksi terbaru
        $transaksiTerbaru = Transaksi::with('kategori', 'user')
            ->latest()
            ->take(5)
            ->get();

        // Data grafik — 6 bulan terakhir
        $bulanList = collect(range(5, 0))->map(function ($i) {
            return now()->subMonths($i)->format('Y-m');
        });

        $grafikMasuk = $bulanList->map(function ($bulan) {
            return Transaksi::where('tipe', 'masuk')
                ->whereYear('tanggal', substr($bulan, 0, 4))
                ->whereMonth('tanggal', substr($bulan, 5, 2))
                ->sum('jumlah');
        });

        $grafikKeluar = $bulanList->map(function ($bulan) {
            return Transaksi::where('tipe', 'keluar')
                ->whereYear('tanggal', substr($bulan, 0, 4))
                ->whereMonth('tanggal', substr($bulan, 5, 2))
                ->sum('jumlah');
        });

        $labelBulan = $bulanList->map(function ($bulan) {
            return \Carbon\Carbon::parse($bulan . '-01')->translatedFormat('M Y');
        });

        // Data pie chart — pengeluaran per kategori (keseluruhan)
        $pengeluaranKategori = Transaksi::where('tipe', 'keluar')
            ->selectRaw('kategori_id, SUM(jumlah) as total')
            ->groupBy('kategori_id')
            ->with('kategori')
            ->orderByDesc('total')
            ->get();

        $labelKategori = $pengeluaranKategori->map(fn($item) => $item->kategori->nama ?? 'Tanpa Kategori');
        $dataKategori  = $pengeluaranKategori->pluck('total');

        // Notifikasi anggaran melebihi batas
        $bulanIni  = now()->month;
        $tahunIni  = now()->year;

        $notifikasiAnggaran = Anggaran::with('kategori')
            ->where('periode_bulan', $bulanIni)
            ->where('periode_tahun', $tahunIni)
            ->get()
            ->map(function ($anggaran) use ($bulanIni, $tahunIni) {
                $realisasi = Transaksi::where('kategori_id', $anggaran->kategori_id)
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
            ->filter(fn($item) => $item['melebihi'] || $item['mendekati'])
            ->values();

        return view('dashboard', compact(
            'totalMasuk',
            'totalKeluar',
            'saldo',
            'transaksiTerbaru',
            'grafikMasuk',
            'grafikKeluar',
            'labelBulan',
            'labelKategori',
            'dataKategori',
            'notifikasiAnggaran'
        ));
    }
}