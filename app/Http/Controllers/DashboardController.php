<?php
namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Anggaran;
use App\Services\AnggaranNotifikasiService;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Total keseluruhan — hanya transaksi yang sudah disetujui
        $totalMasuk  = Transaksi::approved()->where('tipe', 'masuk')->sum('jumlah');
        $totalKeluar = Transaksi::approved()->where('tipe', 'keluar')->sum('jumlah');
        $saldo       = $totalMasuk - $totalKeluar;

        // Transaksi terbaru (tetap tampilkan semua status, termasuk yang menunggu,
        // supaya admin/bendahara bisa lihat ada pengajuan baru langsung dari dashboard)
        $transaksiTerbaru = Transaksi::with('kategori', 'user')
            ->latest()
            ->take(5)
            ->get();

        // Hitung transaksi yang masih menunggu persetujuan, untuk ditampilkan sebagai info/badge di dashboard
        $jumlahMenunggu = Transaksi::menunggu()->count();

        // Data grafik — 6 bulan terakhir — hanya transaksi disetujui
        $bulanList = collect(range(5, 0))->map(function ($i) {
            return now()->subMonths($i)->format('Y-m');
        });

        $grafikMasuk = $bulanList->map(function ($bulan) {
            return Transaksi::approved()
                ->where('tipe', 'masuk')
                ->whereYear('tanggal', substr($bulan, 0, 4))
                ->whereMonth('tanggal', substr($bulan, 5, 2))
                ->sum('jumlah');
        });

        $grafikKeluar = $bulanList->map(function ($bulan) {
            return Transaksi::approved()
                ->where('tipe', 'keluar')
                ->whereYear('tanggal', substr($bulan, 0, 4))
                ->whereMonth('tanggal', substr($bulan, 5, 2))
                ->sum('jumlah');
        });

        $labelBulan = $bulanList->map(function ($bulan) {
            return \Carbon\Carbon::parse($bulan . '-01')->translatedFormat('M Y');
        });

        // Data pie chart — pengeluaran per kategori (keseluruhan) — hanya transaksi disetujui
        $pengeluaranKategori = Transaksi::approved()
            ->where('tipe', 'keluar')
            ->selectRaw('kategori_id, SUM(jumlah) as total')
            ->groupBy('kategori_id')
            ->with('kategori')
            ->orderByDesc('total')
            ->get();

        $labelKategori = $pengeluaranKategori->map(fn($item) => $item->kategori->nama ?? 'Tanpa Kategori');
        $dataKategori  = $pengeluaranKategori->pluck('total');

        // Notifikasi anggaran melebihi batas (logic dipindah ke Service, dipakai juga oleh Bell Icon)
        $notifikasiAnggaran = AnggaranNotifikasiService::aktif();

        return view('dashboard', compact(
            'totalMasuk',
            'totalKeluar',
            'saldo',
            'transaksiTerbaru',
            'jumlahMenunggu',
            'grafikMasuk',
            'grafikKeluar',
            'labelBulan',
            'labelKategori',
            'dataKategori',
            'notifikasiAnggaran'
        ));
    }
}