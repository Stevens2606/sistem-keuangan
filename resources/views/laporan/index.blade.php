@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-lg">
    <div class="bg-white rounded-xl shadow p-6">

        <h2 class="text-xl font-semibold text-gray-800 mb-2">Cetak Laporan PDF</h2>
        <p class="text-sm text-gray-500 mb-6">Pilih bulan untuk mencetak laporan keuangan.</p>

        @if(session('error'))
            <div class="bg-red-100 text-red-700 border border-red-300 rounded-lg px-4 py-3 mb-4">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('laporan.cetak') }}" method="POST">
            @csrf

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Bulan</label>
                <input type="month" name="bulan" value="{{ old('bulan', now()->format('Y-m')) }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('bulan') border-red-500 @enderror">
                @error('bulan')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg font-medium transition flex items-center justify-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                </svg>
                Download PDF
            </button>
        </form>

    </div>
</div>
@endsection