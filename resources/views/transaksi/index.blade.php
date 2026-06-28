@extends('layouts.app')
@section('content')
<div class="container mx-auto px-4 py-6">

    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Daftar Transaksi</h2>
        <a href="{{ route('transaksi.create') }}"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition">
            + Tambah Transaksi
        </a>
    </div>

    {{-- Alert --}}
    @if(session('success'))
        <div class="bg-green-100 text-green-700 border border-green-300 rounded-lg px-4 py-3 mb-4">
            {{ session('success') }}
        </div>
    @endif

    {{-- Ringkasan Saldo --}}
    @php
        $totalMasuk  = $transaksi->where('tipe', 'masuk')->sum('jumlah');
        $totalKeluar = $transaksi->where('tipe', 'keluar')->sum('jumlah');
        $saldo       = $totalMasuk - $totalKeluar;
    @endphp
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-green-50 border border-green-200 rounded-xl p-4">
            <p class="text-sm text-green-600 font-medium">Total Masuk</p>
            <p class="text-lg font-bold text-green-700">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</p>
        </div>
        <div class="bg-red-50 border border-red-200 rounded-xl p-4">
            <p class="text-sm text-red-600 font-medium">Total Keluar</p>
            <p class="text-lg font-bold text-red-700">Rp {{ number_format($totalKeluar, 0, ',', '.') }}</p>
        </div>
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
            <p class="text-sm text-blue-600 font-medium">Saldo</p>
            <p class="text-lg font-bold {{ $saldo >= 0 ? 'text-blue-700' : 'text-red-700' }}">
                Rp {{ number_format($saldo, 0, ',', '.') }}
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
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($transaksi as $t)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-600">
                        {{ $t->tanggal->format('d M Y') }}
                    </td>
                    <td class="px-4 py-3 text-gray-800">{{ $t->keterangan ?? '-' }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $t->kategori->nama ?? '-' }}</td>
                    <td class="px-4 py-3">
                        @if($t->tipe == 'masuk')
                            <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full font-medium">Masuk</span>
                        @else
                            <span class="bg-red-100 text-red-700 text-xs px-2 py-1 rounded-full font-medium">Keluar</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right font-semibold {{ $t->tipe == 'masuk' ? 'text-green-600' : 'text-red-600' }}">
                        Rp {{ number_format($t->jumlah, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('transaksi.edit', $t->id) }}"
                            class="text-blue-600 hover:underline text-xs mr-2">Edit</a>
                        <form action="{{ route('transaksi.destroy', $t->id) }}" method="POST" class="inline"
                            onsubmit="return confirm('Hapus transaksi ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:underline text-xs">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-400">Belum ada transaksi.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection