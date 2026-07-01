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
        Schema::create('invoice', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_invoice', 50)->unique();
            $table->string('nama_klien', 100);
            $table->string('email_klien')->nullable();
            $table->string('telepon_klien', 20)->nullable();
            $table->text('alamat_klien')->nullable();
            $table->date('tanggal_invoice');
            $table->date('tanggal_jatuh_tempo');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('pajak', 5, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->enum('status', ['draft', 'dikirim', 'lunas', 'jatuh_tempo'])->default('draft');
            $table->text('catatan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice');
    }
};
