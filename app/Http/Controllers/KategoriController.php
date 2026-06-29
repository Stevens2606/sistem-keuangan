<?php
namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index()
    {
        $kategori = Kategori::all();
        return view('kategori.index', compact('kategori'));
    }

    public function create()
    {
        return view('kategori.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'      => 'required|string|max:100|unique:kategori,nama',
            'tipe'      => 'required|in:masuk,keluar',
            'deskripsi' => 'nullable|string|max:255',
        ], [
            'nama.required' => 'Nama kategori wajib diisi.',
            'nama.max'      => 'Nama kategori maksimal 100 karakter.',
            'nama.unique'   => 'Nama kategori sudah ada, gunakan nama lain.',
            'tipe.required' => 'Tipe kategori wajib dipilih.',
            'tipe.in'       => 'Tipe harus Masuk atau Keluar.',
            'deskripsi.max' => 'Deskripsi maksimal 255 karakter.',
        ]);

        Kategori::create($request->only('nama', 'tipe', 'deskripsi'));

        return redirect()->route('kategori.index')
            ->with('success', 'Kategori berhasil ditambahkan!');
    }

    public function edit(Kategori $kategori)
    {
        return view('kategori.edit', compact('kategori'));
    }

    public function update(Request $request, Kategori $kategori)
    {
        $request->validate([
            'nama'      => 'required|string|max:100|unique:kategori,nama,' . $kategori->id,
            'tipe'      => 'required|in:masuk,keluar',
            'deskripsi' => 'nullable|string|max:255',
        ], [
            'nama.required' => 'Nama kategori wajib diisi.',
            'nama.max'      => 'Nama kategori maksimal 100 karakter.',
            'nama.unique'   => 'Nama kategori sudah ada, gunakan nama lain.',
            'tipe.required' => 'Tipe kategori wajib dipilih.',
            'tipe.in'       => 'Tipe harus Masuk atau Keluar.',
            'deskripsi.max' => 'Deskripsi maksimal 255 karakter.',
        ]);

        $kategori->update($request->only('nama', 'tipe', 'deskripsi'));

        return redirect()->route('kategori.index')
            ->with('success', 'Kategori berhasil diupdate!');
    }

    public function destroy(Kategori $kategori)
    {
        // Cegah hapus jika masih ada transaksi
        if ($kategori->transaksi()->count() > 0) {
            return redirect()->route('kategori.index')
                ->with('error', 'Kategori tidak bisa dihapus karena masih memiliki transaksi!');
        }

        $kategori->delete();
        return redirect()->route('kategori.index')
            ->with('success', 'Kategori berhasil dihapus!');
    }
}