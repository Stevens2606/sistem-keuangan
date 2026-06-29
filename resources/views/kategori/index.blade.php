@extends('layouts.app')

@section('title', 'Kategori')

@section('content')
<div class="container mx-auto px-4 py-6">

    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Daftar Kategori</h2>
        <a href="{{ route('kategori.create') }}"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition">
            + Tambah Kategori
        </a>
    </div>

       @if(session('success'))
            <div class="bg-green-100 text-green-700 border border-green-300 rounded-lg px-4 py-3 mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 text-red-700 border border-red-300 rounded-lg px-4 py-3 mb-4">
                {{ session('error') }}
            </div>
        @endif

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 text-left">No</th>
                    <th class="px-4 py-3 text-left">Nama</th>
                    <th class="px-4 py-3 text-left">Tipe</th>
                    <th class="px-4 py-3 text-left">Deskripsi</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($kategori as $index => $item)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-500">{{ $index + 1 }}</td>
                    <td class="px-4 py-3 font-medium text-gray-800">{{ $item->nama }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded-full text-xs font-medium
                            {{ $item->tipe == 'masuk' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ ucfirst($item->tipe) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-500">{{ $item->deskripsi ?? '-' }}</td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('kategori.edit', $item) }}"
                            class="text-blue-600 hover:underline text-xs mr-2">Edit</a>
                        <form action="{{ route('kategori.destroy', $item) }}" method="POST"
                            class="inline" onsubmit="return confirm('Yakin hapus?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:underline text-xs">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-400">
                        Belum ada kategori.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection