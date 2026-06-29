@extends('layouts.app')

@section('title', 'Edit Anggaran')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-lg">
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-6">Edit Anggaran</h2>

        <form action="{{ route('anggaran.update', $anggaran->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Kategori & Periode tidak bisa diubah --}}
            <div class="mb-4 p-3 bg-gray-50 rounded-lg border border-gray-200">
                <p class="text-xs text-gray-500 mb-1">Kategori</p>
                <p class="font-medium text-gray-800">{{ $anggaran->kategori->nama ?? '-' }}</p>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                    <p class="text-xs text-gray-500 mb-1">Bulan</p>
                    <p class="font-medium text-gray-800">
                        {{ \Carbon\Carbon::create()->month($anggaran->periode_bulan)->locale('id')->translatedFormat('F') }}
                    </p>
                </div>
                <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                    <p class="text-xs text-gray-500 mb-1">Tahun</p>
                    <p class="font-medium text-gray-800">{{ $anggaran->periode_tahun }}</p>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Anggaran (Rp)</label>
                <input type="number" name="jumlah" value="{{ old('jumlah', $anggaran->jumlah) }}" min="1"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('jumlah') border-red-500 @enderror">
                @error('jumlah')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Keterangan <span class="text-gray-400">(opsional)</span>
                </label>
                <textarea name="keterangan" rows="3"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('keterangan', $anggaran->keterangan) }}</textarea>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-medium transition">
                    Update
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