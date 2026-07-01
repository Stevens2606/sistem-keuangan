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
       Schema::create('jabatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('departemen_id')->constrained('departemen')->onDelete('cascade');
            $table->string('nama', 100);
            $table->enum('level', ['staff', 'supervisor', 'manager', 'direktur'])->default('staff');
            $table->decimal('gaji_pokok', 15, 2)->default(0);
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jabatan');
    }
};
