<?php
namespace App\Http\Controllers;

use App\Models\Anggaran;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ActivityLog;

class AnggaranController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->bulan ?? now()->month;
        $tahun = $request->tahun ?? now()->year;

        $anggarans = Anggaran::with('kategori')
            ->where('periode_bulan', $bulan)
            ->where('periode_tahun', $tahun)
            ->get();

        $kategoris = Kategori::orderBy('nama')->get();

        return view('anggaran.index', compact('anggarans', 'kategoris', 'bulan', 'tahun'));
    }

    public function create()
    {
        $kategoris = Kategori::orderBy('nama')->get();
        return view('anggaran.create', compact('kategoris'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kategori_id'   => 'required|exists:kategori,id',
            'jumlah'        => 'required|numeric|min:1000|max:999999999999',
            'periode_bulan' => 'required|integer|between:1,12',
            'periode_tahun' => 'required|integer|min:2020|max:2035',
            'keterangan'    => 'nullable|string|max:255',
        ], [
            'kategori_id.required'   => 'Kategori wajib dipilih.',
            'kategori_id.exists'     => 'Kategori tidak valid.',
            'jumlah.required'        => 'Jumlah anggaran wajib diisi.',
            'jumlah.numeric'         => 'Jumlah harus berupa angka.',
            'jumlah.min'             => 'Jumlah anggaran minimal Rp 1.000.',
            'jumlah.max'             => 'Jumlah anggaran terlalu besar.',
            'periode_bulan.required' => 'Bulan wajib dipilih.',
            'periode_bulan.between'  => 'Bulan tidak valid (1-12).',
            'periode_tahun.required' => 'Tahun wajib diisi.',
            'periode_tahun.min'      => 'Tahun minimal 2020.',
            'periode_tahun.max'      => 'Tahun maksimal 2035.',
            'keterangan.max'         => 'Keterangan maksimal 255 karakter.',
        ]);

        $exists = Anggaran::where('kategori_id', $request->kategori_id)
            ->where('periode_bulan', $request->periode_bulan)
            ->where('periode_tahun', $request->periode_tahun)
            ->exists();

        if ($exists) {
            return back()->withErrors([
                'kategori_id' => 'Anggaran untuk kategori ini di bulan tersebut sudah ada.'
            ])->withInput();
        }

        $anggaran = Anggaran::create([
            'kategori_id'   => $request->kategori_id,
            'jumlah'        => $request->jumlah,
            'periode_bulan' => $request->periode_bulan,
            'periode_tahun' => $request->periode_tahun,
            'keterangan'    => $request->keterangan,
            'created_by'    => Auth::id(),
        ]);

        ActivityLog::catat('Tambah', 'Anggaran', 'Menambahkan anggaran bulan ' . $anggaran->periode_bulan . '/' . $anggaran->periode_tahun);

        return redirect()->route('anggaran.index')
            ->with('success', 'Anggaran berhasil ditambahkan!');
    }

    public function edit(Anggaran $anggaran)
    {
        $kategoris = Kategori::orderBy('nama')->get();
        return view('anggaran.edit', compact('anggaran', 'kategoris'));
    }

    public function update(Request $request, Anggaran $anggaran)
    {
        $request->validate([
            'jumlah'     => 'required|numeric|min:1000|max:999999999999',
            'keterangan' => 'nullable|string|max:255',
        ], [
            'jumlah.required' => 'Jumlah anggaran wajib diisi.',
            'jumlah.numeric'  => 'Jumlah harus berupa angka.',
            'jumlah.min'      => 'Jumlah anggaran minimal Rp 1.000.',
            'jumlah.max'      => 'Jumlah anggaran terlalu besar.',
            'keterangan.max'  => 'Keterangan maksimal 255 karakter.',
        ]);

        $anggaran->update([
            'jumlah'     => $request->jumlah,
            'keterangan' => $request->keterangan,
        ]);

        ActivityLog::catat('Edit', 'Anggaran', 'Mengedit anggaran bulan ' . $anggaran->periode_bulan . '/' . $anggaran->periode_tahun);

        return redirect()->route('anggaran.index')
            ->with('success', 'Anggaran berhasil diupdate!');
    }

    public function destroy(Anggaran $anggaran)
    {
        $info = 'Anggaran bulan ' . $anggaran->periode_bulan . '/' . $anggaran->periode_tahun;
        $anggaran->delete();

        ActivityLog::catat('Hapus', 'Anggaran', 'Menghapus ' . $info);

        return redirect()->route('anggaran.index')
            ->with('success', 'Anggaran berhasil dihapus!');
    }
}