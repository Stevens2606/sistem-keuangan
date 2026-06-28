<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #1f2937;
        }

        .header {
            text-align: center;
            padding: 20px 0 16px;
            border-bottom: 2px solid #1d4ed8;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 18px;
            font-weight: bold;
            color: #1d4ed8;
        }

        .header p {
            font-size: 12px;
            color: #6b7280;
            margin-top: 4px;
        }

        .ringkasan {
            display: flex;
            gap: 12px;
            margin-bottom: 20px;
        }

        .kartu {
            flex: 1;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 10px 14px;
        }

        .kartu .label {
            font-size: 10px;
            color: #6b7280;
            margin-bottom: 4px;
        }

        .kartu .nilai {
            font-size: 14px;
            font-weight: bold;
        }

        .masuk  { color: #16a34a; }
        .keluar { color: #dc2626; }
        .saldo  { color: #1d4ed8; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        thead tr {
            background-color: #1d4ed8;
            color: white;
        }

        thead th {
            padding: 8px 10px;
            text-align: left;
            font-size: 11px;
        }

        tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }

        tbody td {
            padding: 7px 10px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 11px;
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: bold;
        }

        .badge-masuk  { background: #dcfce7; color: #16a34a; }
        .badge-keluar { background: #fee2e2; color: #dc2626; }

        .text-right { text-align: right; }
        .text-center { text-align: center; }

        .footer {
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 12px;
            margin-top: 8px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Laporan Keuangan</h1>
        <p>Periode: {{ $namaBulan }}</p>
        <p>Dicetak: {{ now()->locale('id')->translatedFormat('d F Y, H:i') }} WIB</p>
    </div>

    {{-- Ringkasan --}}
    <table style="margin-bottom: 20px;">
        <tr>
            <td style="width:33%; padding: 8px 12px; border: 1px solid #e5e7eb; border-radius: 6px;">
                <div style="font-size:10px; color:#6b7280;">Total Masuk</div>
                <div style="font-size:14px; font-weight:bold; color:#16a34a;">
                    Rp {{ number_format($totalMasuk, 0, ',', '.') }}
                </div>
            </td>
            <td style="width:33%; padding: 8px 12px; border: 1px solid #e5e7eb;">
                <div style="font-size:10px; color:#6b7280;">Total Keluar</div>
                <div style="font-size:14px; font-weight:bold; color:#dc2626;">
                    Rp {{ number_format($totalKeluar, 0, ',', '.') }}
                </div>
            </td>
            <td style="width:33%; padding: 8px 12px; border: 1px solid #e5e7eb;">
                <div style="font-size:10px; color:#6b7280;">Saldo</div>
                <div style="font-size:14px; font-weight:bold; color:{{ $saldo >= 0 ? '#1d4ed8' : '#dc2626' }};">
                    Rp {{ number_format($saldo, 0, ',', '.') }}
                </div>
            </td>
        </tr>
    </table>

    {{-- Tabel Transaksi --}}
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Keterangan</th>
                <th>Kategori</th>
                <th>Tipe</th>
                <th class="text-right">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transaksis as $i => $t)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td>{{ $t->tanggal->format('d/m/Y') }}</td>
                <td>{{ $t->keterangan ?? '-' }}</td>
                <td>{{ $t->kategori->nama ?? '-' }}</td>
                <td class="text-center">
                    @if($t->tipe == 'masuk')
                        <span class="badge badge-masuk">Masuk</span>
                    @else
                        <span class="badge badge-keluar">Keluar</span>
                    @endif
                </td>
                <td class="text-right" style="color: {{ $t->tipe == 'masuk' ? '#16a34a' : '#dc2626' }}; font-weight: bold;">
                    Rp {{ number_format($t->jumlah, 0, ',', '.') }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center" style="padding: 20px; color: #9ca3af;">
                    Tidak ada transaksi pada bulan ini.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Laporan ini digenerate otomatis oleh Sistem Keuangan &mdash; {{ now()->format('Y') }}
    </div>

</body>
</html>