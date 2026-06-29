@extends('layouts.app')

@section('title', 'Backup & Restore')

@section('content')
<div class="container mx-auto px-4 py-6">

    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Backup & Restore Database</h2>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 border border-green-300 rounded-lg px-4 py-3 mb-4">
            ✅ {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 text-red-700 border border-red-300 rounded-lg px-4 py-3 mb-4">
            ❌ {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

        {{-- Backup --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <h3 class="text-base font-semibold text-gray-700 dark:text-white mb-2">💾 Buat Backup</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                Download seluruh data database dalam format .sql
            </p>
            <form action="{{ route('backup.create') }}" method="POST">
                @csrf
                <button type="submit"
                    onclick="return confirm('Buat backup database sekarang?')"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-medium transition">
                    💾 Buat Backup Sekarang
                </button>
            </form>
        </div>

        {{-- Restore --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <h3 class="text-base font-semibold text-gray-700 dark:text-white mb-2">🔄 Restore Database</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                Upload file .sql untuk mengembalikan data database
            </p>
            <form action="{{ route('backup.restore') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <input type="file" name="file" accept=".sql,.txt"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @error('file')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit"
                    onclick="return confirm('Restore akan menimpa data saat ini. Lanjutkan?')"
                    class="bg-orange-500 hover:bg-orange-600 text-white px-5 py-2 rounded-lg font-medium transition">
                    🔄 Restore Sekarang
                </button>
            </form>
        </div>

    </div>

    {{-- Daftar file backup --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <h3 class="text-base font-semibold text-gray-700 dark:text-white">📁 File Backup Tersedia</h3>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 text-left">Nama File</th>
                    <th class="px-4 py-3 text-left">Ukuran</th>
                    <th class="px-4 py-3 text-left">Tanggal</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($backups as $backup)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="px-4 py-3 font-medium text-gray-800 dark:text-white">
                        📄 {{ $backup['nama'] }}
                    </td>
                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $backup['ukuran'] }}</td>
                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $backup['tanggal'] }}</td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('backup.download', $backup['nama']) }}"
                            class="text-blue-600 hover:underline text-xs mr-3">⬇️ Download</a>
                        <form action="{{ route('backup.hapus', $backup['nama']) }}" method="POST" class="inline"
                            onsubmit="return confirm('Hapus file backup ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:underline text-xs">🗑️ Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-4 py-8 text-center text-gray-400">
                        Belum ada file backup. Klik "Buat Backup Sekarang" untuk membuat backup pertama.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection