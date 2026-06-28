<?php
namespace App\Http\Controllers;

use App\Models\Anggaran;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            'jumlah'        => 'required|numeric|min:1',
            'periode_bulan' => 'required|integer|between:1,12',
            'periode_tahun' => 'required|integer|min:2000',
            'keterangan'    => 'nullable|string',
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

        Anggaran::create([
            'kategori_id'   => $request->kategori_id,
            'jumlah'        => $request->jumlah,
            'periode_bulan' => $request->periode_bulan,
            'periode_tahun' => $request->periode_tahun,
            'keterangan'    => $request->keterangan,
            'created_by'    => Auth::id(),
        ]);

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
            'jumlah'     => 'required|numeric|min:1',
            'keterangan' => 'nullable|string',
        ]);

        $anggaran->update([
            'jumlah'     => $request->jumlah,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('anggaran.index')
            ->with('success', 'Anggaran berhasil diupdate!');
    }

    public function destroy(Anggaran $anggaran)
    {
        $anggaran->delete();
        return redirect()->route('anggaran.index')
            ->with('success', 'Anggaran berhasil dihapus!');
    }
}