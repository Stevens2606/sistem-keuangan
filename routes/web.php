<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\AnggaranController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\NotifikasiController;
use Illuminate\Support\Facades\Route;

// Halaman welcome
Route::get('/', function () {
    return view('welcome');
});

// Semua route yang butuh login
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Kategori & Transaksi
    Route::resource('kategori', KategoriController::class);
    Route::resource('transaksi', TransaksiController::class);
    Route::get('transaksi/{transaksi}/struk', [TransaksiController::class, 'struk'])->name('transaksi.struk');

    // Approval transaksi keluar — hanya admin (pola sama dengan role:admin di bawah)
    Route::middleware('role:admin')->group(function () {
        Route::patch('transaksi/{transaksi}/approve', [TransaksiController::class, 'approve'])->name('transaksi.approve');
        Route::patch('transaksi/{transaksi}/reject', [TransaksiController::class, 'reject'])->name('transaksi.reject');
    });

    // Notifikasi bell icon (real-time via Reverb)
    Route::get('/notifikasi/anggaran-aktif', [NotifikasiController::class, 'anggaranAktif'])->name('notifikasi.anggaran-aktif');

    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::post('/laporan/cetak', [LaporanController::class, 'cetak'])->name('laporan.cetak');

    Route::resource('anggaran', AnggaranController::class);

    Route::get('export/excel', [ExportController::class, 'exportExcel'])->name('export.excel');

    Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index');
    Route::delete('/activity-log/{activityLog}', [ActivityLogController::class, 'destroy'])->name('activity-log.destroy');
    Route::delete('/activity-log', [ActivityLogController::class, 'destroyAll'])->name('activity-log.destroyAll');

});

// Manajemen User (hanya admin)
Route::resource('users', UserController::class)->middleware('role:admin');


// Backup & Restore (hanya admin)
Route::middleware('role:admin')->group(function () {
    Route::get('backup', [BackupController::class, 'index'])->name('backup.index');
    Route::post('backup/create', [BackupController::class, 'backup'])->name('backup.create');
    Route::get('backup/download/{filename}', [BackupController::class, 'download'])->name('backup.download');
    Route::post('backup/restore', [BackupController::class, 'restore'])->name('backup.restore');
    Route::delete('backup/hapus/{filename}', [BackupController::class, 'hapus'])->name('backup.hapus');
});

require __DIR__.'/auth.php';