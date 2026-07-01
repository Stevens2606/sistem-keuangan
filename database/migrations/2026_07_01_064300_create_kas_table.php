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
       Schema::create('kas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rekening_id')->constrained('rekening')->onDelete('cascade');
            $table->foreignId('transaksi_id')->nullable()->constrained('transaksi')->onDelete('set null');
            $table->enum('tipe', ['masuk', 'keluar']);
            $table->decimal('jumlah', 15, 2);
            $table->decimal('saldo_setelah', 15, 2);
            $table->text('keterangan')->nullable();
            $table->date('tanggal');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kas');
    }
};
