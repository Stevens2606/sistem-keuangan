<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kategori;
use App\Models\Transaksi;
use App\Models\Anggaran;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DataDummySeeder extends Seeder
{
    public function run(): void
    {
        // User Admin
        $user = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name'     => 'Admin',
                'password' => Hash::make('password123'),
            ]
        );

        // Kategori
        $kategoris = [
            ['nama' => 'Gaji',         'tipe' => 'masuk',  'deskripsi' => 'Pendapatan gaji bulanan'],
            ['nama' => 'Penjualan',    'tipe' => 'masuk',  'deskripsi' => 'Hasil penjualan produk'],
            ['nama' => 'Operasional',  'tipe' => 'keluar', 'deskripsi' => 'Biaya operasional kantor'],
            ['nama' => 'Transportasi', 'tipe' => 'keluar', 'deskripsi' => 'Biaya transportasi'],
            ['nama' => 'ATK',          'tipe' => 'keluar', 'deskripsi' => 'Alat tulis kantor'],
            ['nama' => 'Listrik',      'tipe' => 'keluar', 'deskripsi' => 'Tagihan listrik'],
            ['nama' => 'Internet',     'tipe' => 'keluar', 'deskripsi' => 'Tagihan internet'],
        ];

        foreach ($kategoris as $kat) {
            Kategori::firstOrCreate(['nama' => $kat['nama']], $kat);
        }

        $gaji        = Kategori::where('nama', 'Gaji')->first();
        $penjualan   = Kategori::where('nama', 'Penjualan')->first();
        $operasional = Kategori::where('nama', 'Operasional')->first();
        $transport   = Kategori::where('nama', 'Transportasi')->first();
        $atk         = Kategori::where('nama', 'ATK')->first();
        $listrik     = Kategori::where('nama', 'Listrik')->first();
        $internet    = Kategori::where('nama', 'Internet')->first();

        // Transaksi 6 bulan terakhir
        $transaksiData = [
            // Januari
            ['kategori_id' => $gaji->id,        'tipe' => 'masuk',  'jumlah' => 8000000,  'keterangan' => 'Gaji Januari',         'tanggal' => '2026-01-05'],
            ['kategori_id' => $penjualan->id,    'tipe' => 'masuk',  'jumlah' => 3500000,  'keterangan' => 'Penjualan produk A',   'tanggal' => '2026-01-10'],
            ['kategori_id' => $operasional->id,  'tipe' => 'keluar', 'jumlah' => 2000000,  'keterangan' => 'Biaya operasional',    'tanggal' => '2026-01-12'],
            ['kategori_id' => $listrik->id,      'tipe' => 'keluar', 'jumlah' => 450000,   'keterangan' => 'Tagihan listrik Jan',  'tanggal' => '2026-01-15'],
            ['kategori_id' => $transport->id,    'tipe' => 'keluar', 'jumlah' => 300000,   'keterangan' => 'Transport Januar',     'tanggal' => '2026-01-20'],

            // Februari
            ['kategori_id' => $gaji->id,        'tipe' => 'masuk',  'jumlah' => 8000000,  'keterangan' => 'Gaji Februari',        'tanggal' => '2026-02-05'],
            ['kategori_id' => $penjualan->id,    'tipe' => 'masuk',  'jumlah' => 4200000,  'keterangan' => 'Penjualan produk B',   'tanggal' => '2026-02-08'],
            ['kategori_id' => $operasional->id,  'tipe' => 'keluar', 'jumlah' => 1800000,  'keterangan' => 'Biaya operasional',    'tanggal' => '2026-02-10'],
            ['kategori_id' => $atk->id,          'tipe' => 'keluar', 'jumlah' => 250000,   'keterangan' => 'Beli ATK',             'tanggal' => '2026-02-14'],
            ['kategori_id' => $internet->id,     'tipe' => 'keluar', 'jumlah' => 350000,   'keterangan' => 'Tagihan internet Feb', 'tanggal' => '2026-02-20'],

            // Maret
            ['kategori_id' => $gaji->id,        'tipe' => 'masuk',  'jumlah' => 8000000,  'keterangan' => 'Gaji Maret',           'tanggal' => '2026-03-05'],
            ['kategori_id' => $penjualan->id,    'tipe' => 'masuk',  'jumlah' => 5100000,  'keterangan' => 'Penjualan produk C',   'tanggal' => '2026-03-09'],
            ['kategori_id' => $operasional->id,  'tipe' => 'keluar', 'jumlah' => 2200000,  'keterangan' => 'Biaya operasional',    'tanggal' => '2026-03-11'],
            ['kategori_id' => $listrik->id,      'tipe' => 'keluar', 'jumlah' => 480000,   'keterangan' => 'Tagihan listrik Mar',  'tanggal' => '2026-03-15'],
            ['kategori_id' => $transport->id,    'tipe' => 'keluar', 'jumlah' => 400000,   'keterangan' => 'Transport Maret',      'tanggal' => '2026-03-22'],

            // April
            ['kategori_id' => $gaji->id,        'tipe' => 'masuk',  'jumlah' => 8000000,  'keterangan' => 'Gaji April',           'tanggal' => '2026-04-05'],
            ['kategori_id' => $penjualan->id,    'tipe' => 'masuk',  'jumlah' => 3800000,  'keterangan' => 'Penjualan produk D',   'tanggal' => '2026-04-07'],
            ['kategori_id' => $operasional->id,  'tipe' => 'keluar', 'jumlah' => 1900000,  'keterangan' => 'Biaya operasional',    'tanggal' => '2026-04-10'],
            ['kategori_id' => $atk->id,          'tipe' => 'keluar', 'jumlah' => 180000,   'keterangan' => 'Beli ATK April',       'tanggal' => '2026-04-18'],
            ['kategori_id' => $internet->id,     'tipe' => 'keluar', 'jumlah' => 350000,   'keterangan' => 'Tagihan internet Apr', 'tanggal' => '2026-04-20'],

            // Mei
            ['kategori_id' => $gaji->id,        'tipe' => 'masuk',  'jumlah' => 8500000,  'keterangan' => 'Gaji Mei',             'tanggal' => '2026-05-05'],
            ['kategori_id' => $penjualan->id,    'tipe' => 'masuk',  'jumlah' => 6200000,  'keterangan' => 'Penjualan produk E',   'tanggal' => '2026-05-08'],
            ['kategori_id' => $operasional->id,  'tipe' => 'keluar', 'jumlah' => 2500000,  'keterangan' => 'Biaya operasional',    'tanggal' => '2026-05-12'],
            ['kategori_id' => $listrik->id,      'tipe' => 'keluar', 'jumlah' => 500000,   'keterangan' => 'Tagihan listrik Mei',  'tanggal' => '2026-05-15'],
            ['kategori_id' => $transport->id,    'tipe' => 'keluar', 'jumlah' => 450000,   'keterangan' => 'Transport Mei',        'tanggal' => '2026-05-25'],

            // Juni
            ['kategori_id' => $gaji->id,        'tipe' => 'masuk',  'jumlah' => 8500000,  'keterangan' => 'Gaji Juni',            'tanggal' => '2026-06-05'],
            ['kategori_id' => $penjualan->id,    'tipe' => 'masuk',  'jumlah' => 4500000,  'keterangan' => 'Penjualan produk F',   'tanggal' => '2026-06-08'],
            ['kategori_id' => $operasional->id,  'tipe' => 'keluar', 'jumlah' => 2100000,  'keterangan' => 'Biaya operasional',    'tanggal' => '2026-06-10'],
            ['kategori_id' => $atk->id,          'tipe' => 'keluar', 'jumlah' => 220000,   'keterangan' => 'Beli ATK Juni',        'tanggal' => '2026-06-15'],
            ['kategori_id' => $internet->id,     'tipe' => 'keluar', 'jumlah' => 350000,   'keterangan' => 'Tagihan internet Jun', 'tanggal' => '2026-06-20'],
        ];

        foreach ($transaksiData as $t) {
            Transaksi::create(array_merge($t, ['created_by' => $user->id]));
        }

        // Anggaran Juni 2026
        $anggaranData = [
            ['kategori_id' => $operasional->id, 'jumlah' => 3000000, 'periode_bulan' => 6, 'periode_tahun' => 2026, 'keterangan' => 'Anggaran operasional Juni'],
            ['kategori_id' => $transport->id,   'jumlah' => 500000,  'periode_bulan' => 6, 'periode_tahun' => 2026, 'keterangan' => 'Anggaran transport Juni'],
            ['kategori_id' => $atk->id,         'jumlah' => 300000,  'periode_bulan' => 6, 'periode_tahun' => 2026, 'keterangan' => 'Anggaran ATK Juni'],
            ['kategori_id' => $listrik->id,     'jumlah' => 600000,  'periode_bulan' => 6, 'periode_tahun' => 2026, 'keterangan' => 'Anggaran listrik Juni'],
            ['kategori_id' => $internet->id,    'jumlah' => 400000,  'periode_bulan' => 6, 'periode_tahun' => 2026, 'keterangan' => 'Anggaran internet Juni'],
        ];

        foreach ($anggaranData as $a) {
            Anggaran::firstOrCreate(
                ['kategori_id' => $a['kategori_id'], 'periode_bulan' => $a['periode_bulan'], 'periode_tahun' => $a['periode_tahun']],
                array_merge($a, ['created_by' => $user->id])
            );
        }

        $this->command->info('✅ Data dummy berhasil dibuat!');
    }
}