@extends('layouts.app')

@section('title', 'Tambah Kategori')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-lg">
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-6">Tambah Kategori</h2>

        <form action="{{ route('kategori.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kategori</label>
                <input type="text" name="nama" value="{{ old('nama') }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('nama') border-red-500 @enderror"
                    placeholder="Contoh: Gaji, Operasional...">
                @error('nama')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipe</label>
                <select name="tipe"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('tipe') border-red-500 @enderror">
                    <option value="">-- Pilih Tipe --</option>
                    <option value="masuk" {{ old('tipe') == 'masuk' ? 'selected' : '' }}>Masuk</option>
                    <option value="keluar" {{ old('tipe') == 'keluar' ? 'selected' : '' }}>Keluar</option>
                </select>
                @error('tipe')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Deskripsi <span class="text-gray-400">(opsional)</span>
                </label>
                <textarea name="deskripsi" rows="3"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Keterangan tambahan...">{{ old('deskripsi') }}</textarea>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-medium transition">
                    Simpan
                </button>
                <a href="{{ route('kategori.index') }}"
                    class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2 rounded-lg font-medium transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection