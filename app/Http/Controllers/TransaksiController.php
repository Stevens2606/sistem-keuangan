<?php
namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransaksiController extends Controller {
    public function index() {
        $transaksi = Transaksi::with('kategori', 'user')->latest()->get();
        return view('transaksi.index', compact('transaksi'));
    }

    public function create() {
        $kategori = Kategori::all();
        return view('transaksi.create', compact('kategori'));
    }

    public function store(Request $request) {
        $request->validate([
            'kategori_id' => 'required|exists:kategori,id',
            'tipe'        => 'required|in:masuk,keluar',
            'jumlah'      => 'required|numeric|min:1',
            'keterangan'  => 'nullable|string',
            'tanggal'     => 'required|date',
        ]);

        Transaksi::create([
            'kategori_id' => $request->kategori_id,
            'tipe'        => $request->tipe,
            'jumlah'      => $request->jumlah,
            'keterangan'  => $request->keterangan,
            'tanggal'     => $request->tanggal,
            'created_by'  => Auth::id(),
        ]);

        return redirect()->route('transaksi.index')
            ->with('success', 'Transaksi berhasil ditambahkan!');
    }

    public function edit(Transaksi $transaksi) {
        $kategori = Kategori::all();
        return view('transaksi.edit', compact('transaksi', 'kategori'));
    }

    public function update(Request $request, Transaksi $transaksi) {
        $request->validate([
            'kategori_id' => 'required|exists:kategori,id',
            'tipe'        => 'required|in:masuk,keluar',
            'jumlah'      => 'required|numeric|min:1',
            'keterangan'  => 'nullable|string',
            'tanggal'     => 'required|date',
        ]);

        $transaksi->update($request->all());
        return redirect()->route('transaksi.index')
            ->with('success', 'Transaksi berhasil diupdate!');
    }

    public function destroy(Transaksi $transaksi) {
        $transaksi->delete();
        return redirect()->route('transaksi.index')
            ->with('success', 'Transaksi berhasil dihapus!');
    }
}