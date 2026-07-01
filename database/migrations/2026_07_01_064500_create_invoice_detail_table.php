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
        Schema::create('invoice_detail', function (Blueprint $table) {
    $table->id();
    $table->foreignId('invoice_id')->constrained('invoice')->onDelete('cascade');
    $table->string('nama_item', 200);
    $table->text('deskripsi')->nullable();
    $table->integer('qty')->default(1);
    $table->decimal('harga_satuan', 15, 2)->default(0);
    $table->decimal('subtotal', 15, 2)->default(0);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_detail');
    }
};
