<?php
namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Kategori;
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

        return view('dashboard', compact(
            'totalMasuk',
            'totalKeluar',
            'saldo',
            'transaksiTerbaru',
            'grafikMasuk',
            'grafikKeluar',
            'labelBulan'
        ));
    }
}