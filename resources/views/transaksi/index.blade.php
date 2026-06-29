@extends('layouts.app')

@section('title', 'Transaksi')

@section('content')
<div class="container mx-auto px-4 py-6">

    {{-- Header --}}
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold text-gray-800">Daftar Transaksi</h2>
        <div class="flex gap-2">
           <a href="{{ route('export.excel', ['bulan' => date('m'), 'tahun' => date('Y')]) }}"
                class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg font-medium transition text-sm">
                📥 Export Excel
            </a>
            <a href="{{ route('transaksi.create') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition text-sm">
                + Tambah
            </a>
        </div>
    </div>

    {{-- Alert --}}
    @if(session('success'))
        <div class="bg-green-100 text-green-700 border border-green-300 rounded-lg px-4 py-3 mb-4">
            {{ session('success') }}
        </div>
    @endif

    {{-- Filter ringkas --}}
    <div class="bg-white rounded-xl shadow p-4 mb-4">
        <form method="GET" action="{{ route('transaksi.index') }}">
            <div class="grid grid-cols-2 md:grid-cols-6 gap-2 mb-2">
                <div class="col-span-2">
                    <input type="text" name="cari" value="{{ request('cari') }}"
                        placeholder="🔍 Cari keterangan..."
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <select name="tipe" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <option value="">Semua Tipe</option>
                        <option value="masuk"  {{ request('tipe') == 'masuk'  ? 'selected' : '' }}>Masuk</option>
                        <option value="keluar" {{ request('tipe') == 'keluar' ? 'selected' : '' }}>Keluar</option>
                    </select>
                </div>
                <div>
                    <select name="kategori_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <option value="">Semua Kategori</option>
                        @foreach($kategori as $kat)
                            <option value="{{ $kat->id }}" {{ request('kategori_id') == $kat->id ? 'selected' : '' }}>
                                {{ $kat->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <input type="date" name="dari" value="{{ request('dari') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <input type="date" name="sampai" value="{{ request('sampai') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
            </div>
            <div class="flex gap-2">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                    Filter
                </button>
                <a href="{{ route('transaksi.index') }}"
                    class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Ringkasan --}}
    <div class="grid grid-cols-3 gap-4 mb-4">
        <div class="bg-green-50 border border-green-200 rounded-xl p-3">
            <p class="text-xs text-green-600 font-medium">Total Masuk</p>
            <p class="text-lg font-bold text-green-700">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</p>
        </div>
        <div class="bg-red-50 border border-red-200 rounded-xl p-3">
            <p class="text-xs text-red-600 font-medium">Total Keluar</p>
            <p class="text-lg font-bold text-red-700">Rp {{ number_format($totalKeluar, 0, ',', '.') }}</p>
        </div>
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-3">
            <p class="text-xs text-blue-600 font-medium">Selisih</p>
            @php $selisih = $totalMasuk - $totalKeluar; @endphp
            <p class="text-lg font-bold {{ $selisih >= 0 ? 'text-blue-700' : 'text-red-700' }}">
                Rp {{ number_format($selisih, 0, ',', '.') }}
            </p>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="bg-white rounded-xl shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 text-left">Tanggal</th>
                    <th class="px-4 py-3 text-left">Keterangan</th>
                    <th class="px-4 py-3 text-left">Kategori</th>
                    <th class="px-4 py-3 text-left">Tipe</th>
                    <th class="px-4 py-3 text-right">Jumlah</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($transaksi as $t)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-500">{{ $t->tanggal->format('d M Y') }}</td>
                    <td class="px-4 py-3 text-gray-800">
                        {{ $t->keterangan ?? '-' }}
                        @if($t->status == 'ditolak' && $t->catatan_approval)
                            <p class="text-xs text-red-500 mt-0.5">Ditolak: {{ $t->catatan_approval }}</p>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-500">{{ $t->kategori->nama ?? '-' }}</td>
                    <td class="px-4 py-3">
                        @if($t->tipe == 'masuk')
                            <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full">Masuk</span>
                        @else
                            <span class="bg-red-100 text-red-700 text-xs px-2 py-1 rounded-full">Keluar</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right font-semibold {{ $t->tipe == 'masuk' ? 'text-green-600' : 'text-red-600' }}">
                        Rp {{ number_format($t->jumlah, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($t->status == 'menunggu')
                            <span class="bg-yellow-100 text-yellow-700 text-xs px-2 py-1 rounded-full whitespace-nowrap">Menunggu</span>
                        @elseif($t->status == 'disetujui')
                            <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full whitespace-nowrap">Disetujui</span>
                        @else
                            <span class="bg-red-100 text-red-700 text-xs px-2 py-1 rounded-full whitespace-nowrap">Ditolak</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center whitespace-nowrap">
                        @if($t->status != 'ditolak')
                            <a href="{{ route('transaksi.edit', $t->id) }}"
                                class="text-blue-600 hover:underline text-xs mr-2">Edit</a>
                        @endif
                        <a href="{{ route('transaksi.struk', $t->id) }}" target="_blank"
                            class="text-gray-500 hover:underline text-xs mr-2">Cetak</a>

                        @if($t->status == 'menunggu' && auth()->user()->role === 'admin')
                            <form action="{{ route('transaksi.approve', $t->id) }}" method="POST"
                                class="inline" onsubmit="return confirm('Setujui transaksi ini?')">
                                @csrf @method('PATCH')
                                <button type="submit" class="text-green-600 hover:underline text-xs mr-2">Setuju</button>
                            </form>
                            <button type="button" class="text-red-600 hover:underline text-xs mr-2"
                                onclick="document.getElementById('modalTolak{{ $t->id }}').classList.remove('hidden')">
                                Tolak
                            </button>
                        @endif

                        <form action="{{ route('transaksi.destroy', $t->id) }}" method="POST"
                            class="inline" onsubmit="return confirm('Hapus transaksi ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:underline text-xs">Hapus</button>
                        </form>
                    </td>
                </tr>

                {{-- Modal alasan tolak (khusus baris yang masih menunggu) --}}
                @if($t->status == 'menunggu' && auth()->user()->role === 'admin')
                <tr id="modalTolak{{ $t->id }}" class="hidden">
                    <td colspan="7" class="bg-red-50 px-4 py-3 border-t border-red-200">
                        <form action="{{ route('transaksi.reject', $t->id) }}" method="POST">
                            @csrf @method('PATCH')
                            <textarea name="catatan_approval" rows="2" required
                                placeholder="Alasan penolakan (wajib diisi)..."
                                class="w-full border border-red-300 rounded-lg px-3 py-2 text-sm mb-2"></textarea>
                            <div class="flex gap-2">
                                <button type="submit"
                                    class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg text-xs font-medium">
                                    Kirim Penolakan
                                </button>
                                <button type="button"
                                    onclick="document.getElementById('modalTolak{{ $t->id }}').classList.add('hidden')"
                                    class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-2 rounded-lg text-xs font-medium">
                                    Batal
                                </button>
                            </div>
                        </form>
                    </td>
                </tr>
                @endif
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-400">
                        Belum ada transaksi.
                        <a href="{{ route('transaksi.index') }}" class="text-blue-500 hover:underline ml-1">Reset filter</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($transaksi->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $transaksi->links() }}
        </div>
        @endif
    </div>

</div>
@endsection