@extends('layouts.app')

@section('title', 'Tambah Anggaran')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-lg">
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-6">Tambah Anggaran</h2>

        <form action="{{ route('anggaran.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                <select name="kategori_id"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('kategori_id') border-red-500 @enderror">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($kategoris as $kat)
                        <option value="{{ $kat->id }}" {{ old('kategori_id') == $kat->id ? 'selected' : '' }}>
                            {{ $kat->nama }}
                        </option>
                    @endforeach
                </select>
                @error('kategori_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Anggaran (Rp)</label>
                <input type="number" name="jumlah" value="{{ old('jumlah') }}" min="1"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('jumlah') border-red-500 @enderror"
                    placeholder="Contoh: 5000000">
                @error('jumlah')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bulan</label>
                    <select name="periode_bulan"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('periode_bulan') border-red-500 @enderror">
                        @foreach(range(1, 12) as $b)
                            <option value="{{ $b }}" {{ old('periode_bulan', now()->month) == $b ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($b)->locale('id')->translatedFormat('F') }}
                            </option>
                        @endforeach
                    </select>
                    @error('periode_bulan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                    <select name="periode_tahun"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('periode_tahun') border-red-500 @enderror">
                        @foreach(range(now()->year - 1, now()->year + 1) as $t)
                            <option value="{{ $t }}" {{ old('periode_tahun', now()->year) == $t ? 'selected' : '' }}>
                                {{ $t }}
                            </option>
                        @endforeach
                    </select>
                    @error('periode_tahun')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Keterangan <span class="text-gray-400">(opsional)</span>
                </label>
                <textarea name="keterangan" rows="3"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Contoh: Anggaran operasional bulan Juli...">{{ old('keterangan') }}</textarea>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-medium transition">
                    Simpan
                </button>
                <a href="{{ route('anggaran.index') }}"
                    class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2 rounded-lg font-medium transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection