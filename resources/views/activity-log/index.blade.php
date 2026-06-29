@extends('layouts.app')
@section('title', 'Log Aktivitas')

@section('content')
@php
    // Mapping warna badge Bootstrap -> Tailwind, supaya method warnaBadge() di model
    // (yang mengembalikan nama warna Bootstrap seperti 'success', 'danger', dll) tetap bisa dipakai.
    $badgeColors = [
        'success'   => 'bg-green-100 text-green-700',
        'danger'    => 'bg-red-100 text-red-700',
        'warning'   => 'bg-yellow-100 text-yellow-700',
        'info'      => 'bg-blue-100 text-blue-700',
        'primary'   => 'bg-blue-100 text-blue-700',
        'secondary' => 'bg-gray-100 text-gray-700',
    ];
@endphp

<div>
    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h4 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                <i class="bi bi-activity text-blue-600"></i> Log Aktivitas
            </h4>
            <p class="text-sm text-gray-500 mt-0.5">Riwayat semua aksi pengguna dalam sistem</p>
        </div>
        <form action="{{ route('activity-log.destroyAll') }}" method="POST"
              onsubmit="return confirm('Hapus semua log?')">
            @csrf @method('DELETE')
            <button type="submit"
                class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-red-600 border border-red-200 rounded-lg hover:bg-red-50 transition">
                <i class="bi bi-trash"></i> Hapus Semua
            </button>
        </form>
    </div>

    {{-- Filter --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-3">
            <select name="modul" class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">Semua Modul</option>
                @foreach($modulList as $m)
                    <option value="{{ $m }}" {{ request('modul') == $m ? 'selected' : '' }}>{{ $m }}</option>
                @endforeach
            </select>

            <select name="aksi" class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">Semua Aksi</option>
                @foreach($aksiList as $a)
                    <option value="{{ $a }}" {{ request('aksi') == $a ? 'selected' : '' }}>{{ $a }}</option>
                @endforeach
            </select>

            <select name="user_id" class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">Semua User</option>
                @foreach($users as $u)
                    <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                @endforeach
            </select>

            <input type="date" name="dari" value="{{ request('dari') }}"
                class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">

            <input type="date" name="sampai" value="{{ request('sampai') }}"
                class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">

            <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="Cari deskripsi..."
                class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">

            <div class="col-span-full flex gap-2 pt-1">
                <button type="submit"
                    class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition">
                    <i class="bi bi-search"></i> Filter
                </button>
                <a href="{{ route('activity-log.index') }}"
                    class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                    <i class="bi bi-x-circle"></i> Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Tabel --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wide">
                    <tr>
                        <th class="px-4 py-3 text-left">#</th>
                        <th class="px-4 py-3 text-left">Waktu</th>
                        <th class="px-4 py-3 text-left">Pengguna</th>
                        <th class="px-4 py-3 text-left">Aksi</th>
                        <th class="px-4 py-3 text-left">Modul</th>
                        <th class="px-4 py-3 text-left">Deskripsi</th>
                        <th class="px-4 py-3 text-left">IP</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($logs as $i => $log)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 text-gray-400">{{ $logs->firstItem() + $i }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            {{ $log->created_at->format('d M Y') }}<br>
                            <span class="text-gray-400 text-xs">{{ $log->created_at->format('H:i:s') }}</span>
                        </td>
                        <td class="px-4 py-3">{{ $log->user->name ?? 'Sistem' }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center gap-1 text-xs font-medium px-2 py-1 rounded-full {{ $badgeColors[$log->warnaBadge()] ?? 'bg-gray-100 text-gray-700' }}">
                                <i class="bi {{ $log->ikonAksi() }}"></i> {{ $log->aksi }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-xs font-medium px-2 py-1 rounded-full bg-gray-100 text-gray-700">{{ $log->modul }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $log->deskripsi }}</td>
                        <td class="px-4 py-3 text-gray-400 font-mono text-xs">{{ $log->ip_address }}</td>
                        <td class="px-4 py-3">
                            <form action="{{ route('activity-log.destroy', $log) }}" method="POST"
                                  onsubmit="return confirm('Hapus log ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 transition">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-12 text-gray-400">
                            <i class="bi bi-inbox text-3xl block mb-2"></i>
                            Belum ada log aktivitas
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
        <div class="flex justify-between items-center px-4 py-3 border-t border-gray-100">
            <span class="text-xs text-gray-500">
                {{ $logs->firstItem() }}–{{ $logs->lastItem() }} dari {{ $logs->total() }} log
            </span>
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection