@extends('layouts.app')

@section('title', 'Anggaran')

@section('content')
<div class="container mx-auto px-4 py-6">

    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Daftar Anggaran</h2>
        <a href="{{ route('anggaran.create') }}"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition">
            + Tambah Anggaran
        </a>
    </div>

    {{-- Alert --}}
    @if(session('success'))
        <div class="bg-green-100 text-green-700 border border-green-300 rounded-lg px-4 py-3 mb-4">
            {{ session('success') }}
        </div>
    @endif

    {{-- Filter Bulan & Tahun --}}
    <div class="bg-white rounded-xl shadow p-4 mb-6">
        <form method="GET" action="{{ route('anggaran.index') }}" class="flex gap-3 items-end">
            <div>
                <label class="block text-xs text-gray-500 mb-1">Bulan</label>
                <select name="bulan" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    @foreach(range(1, 12) as $b)
                        <option value="{{ $b }}" {{ $bulan == $b ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($b)->locale('id')->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Tahun</label>
                <select name="tahun" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    @foreach(range(now()->year - 2, now()->year + 1) as $t)
                        <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit"
                class="bg-gray-700 hover:bg-gray-800 text-white px-4 py-2 rounded-lg text-sm transition">
                Tampilkan
            </button>
        </form>
    </div>

    {{-- Tabel Anggaran --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 text-left">Kategori</th>
                    <th class="px-4 py-3 text-right">Anggaran</th>
                    <th class="px-4 py-3 text-right">Realisasi</th>
                    <th class="px-4 py-3 text-right">Sisa</th>
                    <th class="px-4 py-3 text-center">Progress</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($anggarans as $a)
                @php
                    $realisasi  = $a->realisasi();
                    $sisa       = $a->jumlah - $realisasi;
                    $persen     = $a->persentase();
                    $warnaBar   = $persen >= 90 ? 'bg-red-500' : ($persen >= 70 ? 'bg-yellow-400' : 'bg-green-500');
                    $warnaSisa  = $sisa < 0 ? 'text-red-600' : 'text-green-600';
                @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <p class="font-medium text-gray-800">{{ $a->kategori->nama ?? '-' }}</p>
                        @if($a->keterangan)
                            <p class="text-xs text-gray-400 mt-0.5">{{ $a->keterangan }}</p>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right font-medium text-gray-700">
                        Rp {{ number_format($a->jumlah, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-3 text-right text-gray-600">
                        Rp {{ number_format($realisasi, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-3 text-right font-semibold {{ $warnaSisa }}">
                        Rp {{ number_format($sisa, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <div class="flex-1 bg-gray-200 rounded-full h-2">
                                <div class="{{ $warnaBar }} h-2 rounded-full transition-all"
                                    style="width: {{ $persen }}%"></div>
                            </div>
                            <span class="text-xs text-gray-500 w-8 text-right">{{ $persen }}%</span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('anggaran.edit', $a->id) }}"
                            class="text-blue-600 hover:underline text-xs mr-2">Edit</a>
                        <form action="{{ route('anggaran.destroy', $a->id) }}" method="POST"
                            class="inline" onsubmit="return confirm('Hapus anggaran ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:underline text-xs">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-10 text-center text-gray-400">
                        Belum ada anggaran untuk bulan ini.
                        <a href="{{ route('anggaran.create') }}" class="text-blue-500 hover:underline ml-1">
                            Tambah sekarang
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection