<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Transaksi #{{ $transaksi->id }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
            .struk-card { box-shadow: none !important; border: none !important; }
        }
        body { font-family: 'Courier New', monospace; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center py-10">

    <div class="w-full max-w-sm">

        {{-- Tombol aksi (hilang saat print) --}}
        <div class="no-print flex gap-2 mb-4">
            <button onclick="window.print()"
                class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2M6 14h12v8H6v-8z" />
                </svg>
                Cetak
            </button>
            <a href="{{ route('transaksi.index') }}"
                class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
                Kembali
            </a>
        </div>

        {{-- Struk --}}
        <div class="struk-card bg-white rounded-lg shadow-md border border-gray-200 p-6 text-sm text-gray-800">

            <div class="text-center mb-4">
                <p class="font-bold text-base uppercase">{{ config('app.name', 'Sistem Keuangan') }}</p>
                <p class="text-xs text-gray-500">Bukti Transaksi Kas</p>
            </div>

            <div class="border-t border-dashed border-gray-300 my-3"></div>

            <table class="w-full text-xs">
                <tr>
                    <td class="py-1 text-gray-500">No. Transaksi</td>
                    <td class="py-1 text-right font-semibold">TRX-{{ str_pad($transaksi->id, 6, '0', STR_PAD_LEFT) }}</td>
                </tr>
                <tr>
                    <td class="py-1 text-gray-500">Tanggal</td>
                    <td class="py-1 text-right">{{ $transaksi->tanggal->format('d M Y') }}</td>
                </tr>
                <tr>
                    <td class="py-1 text-gray-500">Kategori</td>
                    <td class="py-1 text-right">{{ $transaksi->kategori->nama ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="py-1 text-gray-500">Tipe</td>
                    <td class="py-1 text-right">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold
                            {{ $transaksi->tipe == 'masuk' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ ucfirst($transaksi->tipe) }}
                        </span>
                    </td>
                </tr>
            </table>

            <div class="border-t border-dashed border-gray-300 my-3"></div>

            <p class="text-gray-500 text-xs mb-1">Keterangan</p>
            <p class="text-sm mb-3">{{ $transaksi->keterangan ?: '-' }}</p>

            <div class="border-t border-dashed border-gray-300 my-3"></div>

            <div class="flex justify-between items-center mb-1">
                <span class="text-xs text-gray-500">Jumlah</span>
                <span class="text-lg font-bold {{ $transaksi->tipe == 'masuk' ? 'text-green-600' : 'text-red-600' }}">
                    Rp {{ number_format($transaksi->jumlah, 0, ',', '.') }}
                </span>
            </div>

            <div class="border-t border-dashed border-gray-300 my-3"></div>

            <div class="text-xs text-gray-500 space-y-0.5">
                <p>Dicatat oleh: {{ $transaksi->user->name ?? 'Sistem' }}</p>
                <p>Dicetak: {{ now()->format('d M Y H:i') }}</p>
            </div>

            <div class="text-center text-[10px] text-gray-400 mt-4">
                *** Dokumen ini sah tanpa tanda tangan basah ***
            </div>

        </div>
    </div>

</body>
</html>