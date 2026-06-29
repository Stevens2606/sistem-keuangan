<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('transaksi', function (Blueprint $table) {
            // Default 'disetujui' supaya data lama (30 transaksi dummy) otomatis dianggap sudah final,
            // tidak perlu approval ulang dan tidak mengganggu kalkulasi saldo/anggaran yang sudah ada.
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak'])
                ->default('disetujui')
                ->after('tanggal');

            $table->text('catatan_approval')->nullable()->after('status');

            $table->foreignId('approved_by')->nullable()
                ->constrained('users')->onDelete('set null')
                ->after('catatan_approval');

            $table->timestamp('approved_at')->nullable()->after('approved_by');
        });
    }

    public function down(): void {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['status', 'catatan_approval', 'approved_by', 'approved_at']);
        });
    }
};