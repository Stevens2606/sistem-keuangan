<?php
namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Kategori;
use Illuminate\Http\Request;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use App\Events\AnggaranTerlampaui;
use App\Services\AnggaranNotifikasiService;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaksi::with('kategori', 'user');

        if ($request->filled('tipe'))        $query->where('tipe', $request->tipe);
        if ($request->filled('kategori_id')) $query->where('kategori_id', $request->kategori_id);
        if ($request->filled('status'))      $query->where('status', $request->status);
        if ($request->filled('dari'))        $query->whereDate('tanggal', '>=', $request->dari);
        if ($request->filled('sampai'))      $query->whereDate('tanggal', '<=', $request->sampai);
        if ($request->filled('min_nominal')) $query->where('jumlah', '>=', $request->min_nominal);
        if ($request->filled('max_nominal')) $query->where('jumlah', '<=', $request->max_nominal);
        if ($request->filled('cari'))        $query->where('keterangan', 'like', '%' . $request->cari . '%');

        $transaksi = $query->latest('tanggal')->paginate(15)->withQueryString();
        $kategori  = Kategori::orderBy('nama')->get();

        // Total ringkasan tetap hanya menghitung transaksi yang sudah disetujui,
        // supaya konsisten dengan saldo di dashboard, terlepas dari filter status yang dipilih di halaman ini.
        $totalQuery  = Transaksi::approved();
        if ($request->filled('kategori_id')) $totalQuery->where('kategori_id', $request->kategori_id);
        if ($request->filled('dari'))        $totalQuery->whereDate('tanggal', '>=', $request->dari);
        if ($request->filled('sampai'))      $totalQuery->whereDate('tanggal', '<=', $request->sampai);

        $totalMasuk  = $totalQuery->clone()->where('tipe', 'masuk')->sum('jumlah');
        $totalKeluar = $totalQuery->clone()->where('tipe', 'keluar')->sum('jumlah');

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

        // Transaksi keluar wajib melalui persetujuan admin.
        // Transaksi masuk langsung disetujui karena risikonya rendah (dana masuk, bukan keluar).
        $statusAwal = $request->tipe === 'keluar' ? 'menunggu' : 'disetujui';

        $transaksi = Transaksi::create([
            'kategori_id' => $request->kategori_id,
            'tipe'        => $request->tipe,
            'jumlah'      => $request->jumlah,
            'keterangan'  => $request->keterangan,
            'tanggal'     => $request->tanggal,
            'created_by'  => Auth::id(),
            'status'      => $statusAwal,
        ]);

        ActivityLog::catat('Tambah', 'Transaksi', 'Menambahkan transaksi: ' . $transaksi->keterangan);

        // Notifikasi anggaran hanya relevan untuk transaksi keluar yang SUDAH disetujui.
        // Transaksi yang masih menunggu belum mempengaruhi anggaran, jadi tidak perlu broadcast di sini.
        if ($transaksi->tipe === 'keluar' && $transaksi->status === 'disetujui') {
            $notif = AnggaranNotifikasiService::cekSatuKategori($transaksi->kategori_id);
            if ($notif) {
                broadcast(new AnggaranTerlampaui($notif));
            }
        }

        $pesan = $statusAwal === 'menunggu'
            ? 'Transaksi berhasil diajukan dan menunggu persetujuan admin.'
            : 'Transaksi berhasil ditambahkan!';

        return redirect()->route('transaksi.index')->with('success', $pesan);
    }

    public function edit(Transaksi $transaksi)
    {
        // Transaksi yang sudah ditolak bersifat final — tidak bisa diedit/diajukan ulang.
        // Bendahara harus membuat transaksi baru.
        if ($transaksi->status === 'ditolak') {
            return redirect()->route('transaksi.index')
                ->with('error', 'Transaksi yang sudah ditolak tidak dapat diedit. Silakan buat transaksi baru.');
        }

        $kategori = Kategori::orderBy('nama')->get();
        return view('transaksi.edit', compact('transaksi', 'kategori'));
    }

    public function update(Request $request, Transaksi $transaksi)
    {
        if ($transaksi->status === 'ditolak') {
            return redirect()->route('transaksi.index')
                ->with('error', 'Transaksi yang sudah ditolak tidak dapat diedit. Silakan buat transaksi baru.');
        }

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

        // Jika transaksi keluar yang sudah disetujui diedit (misal nominalnya diubah admin),
        // statusnya dikembalikan ke 'menunggu' supaya perubahan tetap melalui review ulang.
        $statusBaru = $transaksi->status;
        if ($request->tipe === 'keluar' && $transaksi->status === 'disetujui') {
            $statusBaru = 'menunggu';
        } elseif ($request->tipe === 'masuk') {
            $statusBaru = 'disetujui';
        }

        $transaksi->update([
            'kategori_id' => $request->kategori_id,
            'tipe'        => $request->tipe,
            'jumlah'      => $request->jumlah,
            'keterangan'  => $request->keterangan,
            'tanggal'     => $request->tanggal,
            'status'      => $statusBaru,
        ]);

        ActivityLog::catat('Edit', 'Transaksi', 'Mengedit transaksi: ' . $transaksi->keterangan);

        if ($transaksi->tipe === 'keluar' && $transaksi->status === 'disetujui') {
            $notif = AnggaranNotifikasiService::cekSatuKategori($transaksi->kategori_id);
            if ($notif) {
                broadcast(new AnggaranTerlampaui($notif));
            }
        }

        $pesan = $statusBaru === 'menunggu'
            ? 'Transaksi berhasil diupdate dan menunggu persetujuan ulang admin.'
            : 'Transaksi berhasil diupdate!';

        return redirect()->route('transaksi.index')->with('success', $pesan);
    }

    public function destroy(Transaksi $transaksi)
    {
        $nama = $transaksi->keterangan;
        $transaksi->delete();

        ActivityLog::catat('Hapus', 'Transaksi', 'Menghapus transaksi: ' . $nama);

        return redirect()->route('transaksi.index')
            ->with('success', 'Transaksi berhasil dihapus!');
    }

    /**
     * Setujui transaksi yang masih menunggu (khusus admin — diberlakukan via middleware/policy di route).
     */
    public function approve(Transaksi $transaksi)
    {
        if ($transaksi->status !== 'menunggu') {
            return redirect()->route('transaksi.index')
                ->with('error', 'Transaksi ini sudah diproses sebelumnya.');
        }

        $transaksi->update([
            'status'      => 'disetujui',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'catatan_approval' => null,
        ]);

        ActivityLog::catat('Setuju', 'Transaksi', 'Menyetujui transaksi: ' . $transaksi->keterangan);

        // Setelah disetujui, baru relevan untuk dicek terhadap batas anggaran.
        if ($transaksi->tipe === 'keluar') {
            $notif = AnggaranNotifikasiService::cekSatuKategori($transaksi->kategori_id);
            if ($notif) {
                broadcast(new AnggaranTerlampaui($notif));
            }
        }

        return redirect()->route('transaksi.index')
            ->with('success', 'Transaksi berhasil disetujui.');
    }

    /**
     * Tolak transaksi yang masih menunggu, dengan catatan alasan (khusus admin).
     */
    public function reject(Request $request, Transaksi $transaksi)
    {
        if ($transaksi->status !== 'menunggu') {
            return redirect()->route('transaksi.index')
                ->with('error', 'Transaksi ini sudah diproses sebelumnya.');
        }

        $request->validate([
            'catatan_approval' => 'required|string|max:500',
        ], [
            'catatan_approval.required' => 'Alasan penolakan wajib diisi.',
            'catatan_approval.max'      => 'Alasan penolakan maksimal 500 karakter.',
        ]);

        $transaksi->update([
            'status'           => 'ditolak',
            'approved_by'      => Auth::id(),
            'approved_at'      => now(),
            'catatan_approval' => $request->catatan_approval,
        ]);

        ActivityLog::catat('Tolak', 'Transaksi', 'Menolak transaksi: ' . $transaksi->keterangan . ' — Alasan: ' . $request->catatan_approval);

        return redirect()->route('transaksi.index')
            ->with('success', 'Transaksi berhasil ditolak.');
    }

    /**
     * Tampilkan struk cetak untuk satu transaksi.
     */
    public function struk(Transaksi $transaksi)
    {
        $transaksi->load('kategori', 'user');

        return view('transaksi.struk', compact('transaksi'));
    }
}