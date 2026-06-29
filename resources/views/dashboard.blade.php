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

    {{-- Notifikasi Anggaran --}}
        @if($notifikasiAnggaran->count() > 0)
        <div class="mb-6">
            <h3 class="text-base font-semibold text-gray-700 mb-3">⚠️ Peringatan Anggaran Bulan Ini</h3>
            <div class="space-y-2">
                @foreach($notifikasiAnggaran as $notif)
                <div class="flex items-center justify-between px-4 py-3 rounded-xl border
                    {{ $notif['melebihi'] ? 'bg-red-50 border-red-300' : 'bg-yellow-50 border-yellow-300' }}">
                    <div class="flex items-center gap-3">
                        <span class="text-xl">{{ $notif['melebihi'] ? '🚨' : '⚠️' }}</span>
                        <div>
                            <p class="text-sm font-semibold {{ $notif['melebihi'] ? 'text-red-700' : 'text-yellow-700' }}">
                                {{ $notif['kategori'] }}
                                {{ $notif['melebihi'] ? '— MELEBIHI ANGGARAN!' : '— Mendekati Batas' }}
                            </p>
                            <p class="text-xs text-gray-500">
                                Anggaran: Rp {{ number_format($notif['anggaran'], 0, ',', '.') }} |
                                Realisasi: Rp {{ number_format($notif['realisasi'], 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold {{ $notif['melebihi'] ? 'text-red-600' : 'text-yellow-600' }}">
                            {{ $notif['persentase'] }}%
                        </p>
                        <div class="w-24 bg-gray-200 rounded-full h-2 mt-1">
                            <div class="h-2 rounded-full {{ $notif['melebihi'] ? 'bg-red-500' : 'bg-yellow-400' }}"
                                style="width: {{ min($notif['persentase'], 100) }}%"></div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    {{-- Grafik --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow p-6 lg:col-span-2">
            <h3 class="text-base font-semibold text-gray-700 mb-4">Grafik 6 Bulan Terakhir</h3>
            <canvas id="grafikKeuangan" height="100"></canvas>
        </div>

        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-base font-semibold text-gray-700 mb-4">Pengeluaran per Kategori</h3>
            @if($dataKategori->count() > 0)
                <div style="height: 260px;">
                    <canvas id="pieKategori"></canvas>
                </div>
            @else
                <p class="text-sm text-gray-400 text-center py-10">Belum ada data pengeluaran</p>
            @endif
        </div>
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

{{-- Pie Chart Kategori --}}
@if($dataKategori->count() > 0)
<script>
    const paletKategori = [
        '#3B82F6', '#22C55E', '#F59E0B', '#EF4444', '#8B5CF6',
        '#06B6D4', '#EC4899', '#84CC16', '#F97316', '#6366F1'
    ];

    const ctxPie = document.getElementById('pieKategori').getContext('2d');
    new Chart(ctxPie, {
        type: 'pie',
        data: {
            labels: @json($labelKategori),
            datasets: [{
                data: @json($dataKategori),
                backgroundColor: paletKategori,
                borderWidth: 1,
                borderColor: '#ffffff',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { boxWidth: 12, font: { size: 11 } }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const persen = ((context.raw / total) * 100).toFixed(1);
                            return ' ' + context.label + ': Rp ' + context.raw.toLocaleString('id-ID') + ' (' + persen + '%)';
                        }
                    }
                }
            }
        }
    });
</script>
@endif
@endsection