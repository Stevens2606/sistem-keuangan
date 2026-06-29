<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BackupController extends Controller {

    public function index() {
        $backups = [];
        $backupPath = storage_path('app/backups');

        if (file_exists($backupPath)) {
            $files = glob($backupPath . '/*.sql');
            foreach ($files as $file) {
                $backups[] = [
                    'nama' => basename($file),
                    'ukuran' => round(filesize($file) / 1024, 2) . ' KB',
                    'tanggal' => date('d M Y H:i', filemtime($file)),
                ];
            }
            arsort($backups);
        }

        return view('backup.index', compact('backups'));
    }

    public function backup() {
        $backupPath = storage_path('app/backups');
        if (!file_exists($backupPath)) {
            mkdir($backupPath, 0755, true);
        }

        $filename = 'backup-' . date('Y-m-d-H-i-s') . '.sql';
        $filepath = $backupPath . '/' . $filename;

        $dbName = config('database.connections.mysql.database');
        $dbUser = config('database.connections.mysql.username');
        $dbPass = config('database.connections.mysql.password');
        $dbHost = config('database.connections.mysql.host');

        $mysqldump = 'C:\xampp\mysql\bin\mysqldump.exe';
        $command = "\"$mysqldump\" --user=$dbUser --host=$dbHost $dbName > \"$filepath\"";

        if ($dbPass) {
            $command = "\"$mysqldump\" --user=$dbUser --password=$dbPass --host=$dbHost $dbName > \"$filepath\"";
        }

        exec($command, $output, $returnCode);

        if ($returnCode === 0 && file_exists($filepath)) {
            return redirect()->route('backup.index')
                ->with('success', 'Backup berhasil dibuat: ' . $filename);
        }

        return redirect()->route('backup.index')
            ->with('error', 'Backup gagal. Pastikan mysqldump tersedia.');
    }

    public function download($filename) {
        $filepath = storage_path('app/backups/' . $filename);
        if (!file_exists($filepath)) {
            abort(404);
        }
        return response()->download($filepath);
    }

    public function restore(Request $request) {
        $request->validate([
            'file' => 'required|file|mimes:sql,txt'
        ]);

        $file = $request->file('file');
        $filepath = $file->getPathname();

        $dbName = config('database.connections.mysql.database');
        $dbUser = config('database.connections.mysql.username');
        $dbPass = config('database.connections.mysql.password');
        $dbHost = config('database.connections.mysql.host');

        $mysql = 'C:\xampp\mysql\bin\mysql.exe';
        $command = "\"$mysql\" --user=$dbUser --host=$dbHost $dbName < \"$filepath\"";

        if ($dbPass) {
            $command = "\"$mysql\" --user=$dbUser --password=$dbPass --host=$dbHost $dbName < \"$filepath\"";
        }

        exec($command, $output, $returnCode);

        if ($returnCode === 0) {
            return redirect()->route('backup.index')
                ->with('success', 'Restore database berhasil!');
        }

        return redirect()->route('backup.index')
            ->with('error', 'Restore gagal. Pastikan file SQL valid.');
    }

    public function hapus($filename) {
        $filepath = storage_path('app/backups/' . $filename);
        if (file_exists($filepath)) {
            unlink($filepath);
        }
        return redirect()->route('backup.index')
            ->with('success', 'File backup berhasil dihapus!');
    }
}