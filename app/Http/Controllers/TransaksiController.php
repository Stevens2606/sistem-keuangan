<?php
namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Kategori;
use Illuminate\Http\Request;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaksi::with('kategori', 'user');

        if ($request->filled('tipe'))        $query->where('tipe', $request->tipe);
        if ($request->filled('kategori_id')) $query->where('kategori_id', $request->kategori_id);
        if ($request->filled('dari'))        $query->whereDate('tanggal', '>=', $request->dari);
        if ($request->filled('sampai'))      $query->whereDate('tanggal', '<=', $request->sampai);
        if ($request->filled('min_nominal')) $query->where('jumlah', '>=', $request->min_nominal);
        if ($request->filled('max_nominal')) $query->where('jumlah', '<=', $request->max_nominal);
        if ($request->filled('cari'))        $query->where('keterangan', 'like', '%' . $request->cari . '%');

        $transaksi   = $query->latest('tanggal')->paginate(15)->withQueryString();
        $kategori    = Kategori::orderBy('nama')->get();
        $totalMasuk  = $query->clone()->where('tipe', 'masuk')->sum('jumlah');
        $totalKeluar = $query->clone()->where('tipe', 'keluar')->sum('jumlah');

        return view('transaksi.index', compact('transaksi', 'kategori', 'totalMasuk', 'totalKeluar'));
    }

    public function create()
    {
        $kategori = Kategori::orderBy('nama')->get();
        return view('transaksi.create', compact('kategori'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kategori_id' => 'required|exists:kategori,id',
            'tipe'        => 'required|in:masuk,keluar',
            'jumlah'      => 'required|numeric|min:1|max:999999999999',
            'keterangan'  => 'nullable|string|max:500',
            'tanggal'     => 'required|date|before_or_equal:today',
        ], [
            'kategori_id.required'    => 'Kategori wajib dipilih.',
            'kategori_id.exists'      => 'Kategori tidak valid.',
            'tipe.required'           => 'Tipe transaksi wajib dipilih.',
            'tipe.in'                 => 'Tipe harus Masuk atau Keluar.',
            'jumlah.required'         => 'Jumlah wajib diisi.',
            'jumlah.numeric'          => 'Jumlah harus berupa angka.',
            'jumlah.min'              => 'Jumlah minimal Rp 1.',
            'jumlah.max'              => 'Jumlah terlalu besar.',
            'keterangan.max'          => 'Keterangan maksimal 500 karakter.',
            'tanggal.required'        => 'Tanggal wajib diisi.',
            'tanggal.date'            => 'Format tanggal tidak valid.',
            'tanggal.before_or_equal' => 'Tanggal tidak boleh melebihi hari ini.',
        ]);

        $transaksi = Transaksi::create([
            'kategori_id' => $request->kategori_id,
            'tipe'        => $request->tipe,
            'jumlah'      => $request->jumlah,
            'keterangan'  => $request->keterangan,
            'tanggal'     => $request->tanggal,
            'created_by'  => Auth::id(),
        ]);

        ActivityLog::catat('Tambah', 'Transaksi', 'Menambahkan transaksi: ' . $transaksi->keterangan);

        return redirect()->route('transaksi.index')
            ->with('success', 'Transaksi berhasil ditambahkan!');
    }

    public function edit(Transaksi $transaksi)
    {
        $kategori = Kategori::orderBy('nama')->get();
        return view('transaksi.edit', compact('transaksi', 'kategori'));
    }

    public function update(Request $request, Transaksi $transaksi)
    {
        $request->validate([
            'kategori_id' => 'required|exists:kategori,id',
            'tipe'        => 'required|in:masuk,keluar',
            'jumlah'      => 'required|numeric|min:1|max:999999999999',
            'keterangan'  => 'nullable|string|max:500',
            'tanggal'     => 'required|date|before_or_equal:today',
        ], [
            'kategori_id.required'    => 'Kategori wajib dipilih.',
            'kategori_id.exists'      => 'Kategori tidak valid.',
            'tipe.required'           => 'Tipe transaksi wajib dipilih.',
            'tipe.in'                 => 'Tipe harus Masuk atau Keluar.',
            'jumlah.required'         => 'Jumlah wajib diisi.',
            'jumlah.numeric'          => 'Jumlah harus berupa angka.',
            'jumlah.min'              => 'Jumlah minimal Rp 1.',
            'jumlah.max'              => 'Jumlah terlalu besar.',
            'keterangan.max'          => 'Keterangan maksimal 500 karakter.',
            'tanggal.required'        => 'Tanggal wajib diisi.',
            'tanggal.date'            => 'Format tanggal tidak valid.',
            'tanggal.before_or_equal' => 'Tanggal tidak boleh melebihi hari ini.',
        ]);

        $transaksi->update([
            'kategori_id' => $request->kategori_id,
            'tipe'        => $request->tipe,
            'jumlah'      => $request->jumlah,
            'keterangan'  => $request->keterangan,
            'tanggal'     => $request->tanggal,
        ]);

        ActivityLog::catat('Edit', 'Transaksi', 'Mengedit transaksi: ' . $transaksi->keterangan);

        return redirect()->route('transaksi.index')
            ->with('success', 'Transaksi berhasil diupdate!');
    }

    public function destroy(Transaksi $transaksi)
    {
        $nama = $transaksi->keterangan;
        $transaksi->delete();

        ActivityLog::catat('Hapus', 'Transaksi', 'Menghapus transaksi: ' . $nama);

        return redirect()->route('transaksi.index')
            ->with('success', 'Transaksi berhasil dihapus!');
    }
}