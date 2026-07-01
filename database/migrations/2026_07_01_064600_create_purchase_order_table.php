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
       Schema::create('purchase_order', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_po', 50)->unique();
            $table->foreignId('supplier_id')->constrained('supplier')->onDelete('restrict');
            $table->date('tanggal_po');
            $table->date('tanggal_kirim')->nullable();
            $table->decimal('total', 15, 2)->default(0);
            $table->enum('status', ['draft', 'dikirim', 'diterima', 'dibatalkan'])->default('draft');
            $table->text('catatan')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order');
    }
};
