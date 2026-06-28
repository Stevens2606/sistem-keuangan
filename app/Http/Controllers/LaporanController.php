<?php
namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function index()
    {
        return view('laporan.index');
    }

    public function cetak(Request $request)
    {
        $request->validate([
            'bulan' => 'required',
        ]);

        $bulan = $request->bulan; // format: 2024-06
        $tahun = substr($bulan, 0, 4);
        $bln   = substr($bulan, 5, 2);

        $transaksis = Transaksi::with('kategori', 'user')
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bln)
            ->orderBy('tanggal')
            ->get();

        $totalMasuk  = $transaksis->where('tipe', 'masuk')->sum('jumlah');
        $totalKeluar = $transaksis->where('tipe', 'keluar')->sum('jumlah');
        $saldo       = $totalMasuk - $totalKeluar;

        $namaBulan = Carbon::parse($bulan . '-01')
            ->locale('id')
            ->translatedFormat('F Y');

        $pdf = Pdf::loadView('laporan.pdf', compact(
            'transaksis',
            'totalMasuk',
            'totalKeluar',
            'saldo',
            'namaBulan'
        ))->setPaper('a4', 'portrait');

        return $pdf->download("laporan-keuangan-{$bulan}.pdf");
    }
}