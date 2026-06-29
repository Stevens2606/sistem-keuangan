<?php
namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ExportController extends Controller {
    public function exportExcel(Request $request) {
        $bulan = $request->bulan ?? date('m');
        $tahun = $request->tahun ?? date('Y');

        $transaksi = Transaksi::with('kategori')
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Transaksi');

        // Header
        $sheet->setCellValue('A1', 'LAPORAN KEUANGAN');
        $sheet->setCellValue('A2', 'Periode: ' . date('F Y', mktime(0,0,0,$bulan,1,$tahun)));
        $sheet->mergeCells('A1:F1');
        $sheet->mergeCells('A2:F2');

        // Style header utama
        $sheet->getStyle('A1:F2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1a56db']],
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
        ]);

        // Kolom header tabel
        $headers = ['No', 'Tanggal', 'Kategori', 'Tipe', 'Keterangan', 'Jumlah'];
        $cols = ['A', 'B', 'C', 'D', 'E', 'F'];
        foreach ($headers as $i => $header) {
            $sheet->setCellValue($cols[$i] . '4', $header);
        }

        $sheet->getStyle('A4:F4')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'e2e8f0']],
        ]);

        // Data transaksi
        $row = 5;
        $totalMasuk = 0;
        $totalKeluar = 0;

        foreach ($transaksi as $i => $t) {
            $sheet->setCellValue('A' . $row, $i + 1);
            $sheet->setCellValue('B' . $row, $t->tanggal->format('d/m/Y'));
            $sheet->setCellValue('C' . $row, $t->kategori->nama);
            $sheet->setCellValue('D' . $row, ucfirst($t->tipe));
            $sheet->setCellValue('E' . $row, $t->keterangan ?? '-');
            $sheet->setCellValue('F' . $row, $t->jumlah);

            if ($t->tipe == 'masuk') {
                $totalMasuk += $t->jumlah;
                $sheet->getStyle('D' . $row)->getFont()->getColor()->setRGB('16a34a');
            } else {
                $totalKeluar += $t->jumlah;
                $sheet->getStyle('D' . $row)->getFont()->getColor()->setRGB('dc2626');
            }
            $row++;
        }

        // Total
        $sheet->setCellValue('E' . ($row + 1), 'Total Masuk:');
        $sheet->setCellValue('F' . ($row + 1), $totalMasuk);
        $sheet->setCellValue('E' . ($row + 2), 'Total Keluar:');
        $sheet->setCellValue('F' . ($row + 2), $totalKeluar);
        $sheet->setCellValue('E' . ($row + 3), 'Saldo:');
        $sheet->setCellValue('F' . ($row + 3), $totalMasuk - $totalKeluar);

        $sheet->getStyle('E' . ($row+1) . ':F' . ($row+3))->getFont()->setBold(true);

        // Auto size kolom
        foreach ($cols as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Download
        $filename = 'laporan-keuangan-' . date('F-Y', mktime(0,0,0,$bulan,1,$tahun)) . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}