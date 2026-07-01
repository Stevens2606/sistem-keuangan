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
        Schema::create('rekening', function (Blueprint $table) {
            $table->id();
            $table->string('nama_bank', 100);
            $table->string('nomor_rekening', 50)->unique();
            $table->string('atas_nama', 100);
            $table->decimal('saldo_awal', 15, 2)->default(0);
            $table->decimal('saldo_saat_ini', 15, 2)->default(0);
            $table->enum('jenis', ['giro', 'tabungan', 'kas_tunai'])->default('tabungan');
            $table->boolean('is_active')->default(true);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rekening');
    }
};
