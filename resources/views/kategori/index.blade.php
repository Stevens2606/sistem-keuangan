<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Manajemen Kategori
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                @if(session('success'))
                    <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="flex justify-between mb-4">
                    <h3 class="text-lg font-semibold">Daftar Kategori</h3>
                    <a href="{{ route('kategori.create') }}" 
                       class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                        + Tambah Kategori
                    </a>
                </div>

                <table class="w-full border-collapse border border-gray-300">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border p-3 text-left">No</th>
                            <th class="border p-3 text-left">Nama</th>
                            <th class="border p-3 text-left">Tipe</th>
                            <th class="border p-3 text-left">Deskripsi</th>
                            <th class="border p-3 text-left">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kategori as $index => $item)
                        <tr class="hover:bg-gray-50">
                            <td class="border p-3">{{ $index + 1 }}</td>
                            <td class="border p-3">{{ $item->nama }}</td>
                            <td class="border p-3">
                                <span class="px-2 py-1 rounded text-sm
                                    {{ $item->tipe == 'masuk' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($item->tipe) }}
                                </span>
                            </td>
                            <td class="border p-3">{{ $item->deskripsi ?? '-' }}</td>
                            <td class="border p-3">
                                <a href="{{ route('kategori.edit', $item) }}" 
                                   class="bg-yellow-400 text-white px-3 py-1 rounded text-sm hover:bg-yellow-500">
                                    Edit
                                </a>
                                <form action="{{ route('kategori.destroy', $item) }}" 
                                      method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            onclick="return confirm('Yakin hapus?')"
                                            class="bg-red-500 text-white px-3 py-1 rounded text-sm hover:bg-red-600">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="border p-3 text-center text-gray-500">
                                Belum ada kategori
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>