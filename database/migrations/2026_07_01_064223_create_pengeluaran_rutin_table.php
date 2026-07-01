<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pengeluaran_rutin', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->foreignId('kategori_id')->constrained('kategori')->onDelete('restrict');
            $table->decimal('jumlah', 15, 2);
            $table->enum('frekuensi', ['harian', 'mingguan', 'bulanan', 'tahunan'])->default('bulanan');
            $table->integer('tanggal_bayar')->nullable()->comment('Tanggal dalam bulan');
            $table->boolean('is_active')->default(true);
            $table->text('keterangan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengeluaran_rutin');
    }
};
