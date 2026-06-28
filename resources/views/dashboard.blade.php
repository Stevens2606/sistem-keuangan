@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">

    <h2 class="text-xl font-semibold text-gray-800 mb-6">Dashboard Keuangan</h2>

    {{-- Kartu Ringkasan --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">

        <div class="bg-white rounded-xl shadow p-5 border-l-4 border-green-500">
            <p class="text-sm text-gray-500 mb-1">Total Masuk</p>
            <p class="text-2xl font-bold text-green-600">
                Rp {{ number_format($totalMasuk, 0, ',', '.') }}
            </p>
            <p class="text-xs text-gray-400 mt-1">Seluruh pemasukan</p>
        </div>

        <div class="bg-white rounded-xl shadow p-5 border-l-4 border-red-500">
            <p class="text-sm text-gray-500 mb-1">Total Keluar</p>
            <p class="text-2xl font-bold text-red-600">
                Rp {{ number_format($totalKeluar, 0, ',', '.') }}
            </p>
            <p class="text-xs text-gray-400 mt-1">Seluruh pengeluaran</p>
        </div>

        <div class="bg-white rounded-xl shadow p-5 border-l-4 {{ $saldo >= 0 ? 'border-blue-500' : 'border-orange-500' }}">
            <p class="text-sm text-gray-500 mb-1">Saldo</p>
            <p class="text-2xl font-bold {{ $saldo >= 0 ? 'text-blue-600' : 'text-orange-600' }}">
                Rp {{ number_format($saldo, 0, ',', '.') }}
            </p>
            <p class="text-xs text-gray-400 mt-1">Masuk - Keluar</p>
        </div>

    </div>

    {{-- Grafik --}}
    <div class="bg-white rounded-xl shadow p-6 mb-8">
        <h3 class="text-base font-semibold text-gray-700 mb-4">Grafik 6 Bulan Terakhir</h3>
        <canvas id="grafikKeuangan" height="100"></canvas>
    </div>

    {{-- Transaksi Terbaru --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="flex justify-between items-center px-6 py-4 border-b border-gray-100">
            <h3 class="text-base font-semibold text-gray-700">Transaksi Terbaru</h3>
            <a href="{{ route('transaksi.index') }}"
                class="text-sm text-blue-600 hover:underline">Lihat semua →</a>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                <tr>
                    <th class="px-6 py-3 text-left">Tanggal</th>
                    <th class="px-6 py-3 text-left">Keterangan</th>
                    <th class="px-6 py-3 text-left">Kategori</th>
                    <th class="px-6 py-3 text-left">Tipe</th>
                    <th class="px-6 py-3 text-right">Jumlah</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($transaksiTerbaru as $t)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-3 text-gray-500">
                        {{ $t->tanggal->format('d M Y') }}
                    </td>
                    <td class="px-6 py-3 text-gray-800">{{ $t->keterangan ?? '-' }}</td>
                    <td class="px-6 py-3 text-gray-500">{{ $t->kategori->nama ?? '-' }}</td>
                    <td class="px-6 py-3">
                        @if($t->tipe == 'masuk')
                            <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full">Masuk</span>
                        @else
                            <span class="bg-red-100 text-red-700 text-xs px-2 py-1 rounded-full">Keluar</span>
                        @endif
                    </td>
                    <td class="px-6 py-3 text-right font-semibold {{ $t->tipe == 'masuk' ? 'text-green-600' : 'text-red-600' }}">
                        Rp {{ number_format($t->jumlah, 0, ',', '.') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-400">
                        Belum ada transaksi.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('grafikKeuangan').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($labelBulan),
            datasets: [
                {
                    label: 'Masuk',
                    data: @json($grafikMasuk),
                    backgroundColor: 'rgba(34, 197, 94, 0.7)',
                    borderColor: 'rgba(34, 197, 94, 1)',
                    borderWidth: 1,
                    borderRadius: 4,
                },
                {
                    label: 'Keluar',
                    data: @json($grafikKeluar),
                    backgroundColor: 'rgba(239, 68, 68, 0.7)',
                    borderColor: 'rgba(239, 68, 68, 1)',
                    borderWidth: 1,
                    borderRadius: 4,
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return ' Rp ' + context.raw.toLocaleString('id-ID');
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                }
            }
        }
    });
</script>
@endsection 