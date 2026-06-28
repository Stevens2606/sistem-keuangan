<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategori extends Model {
    protected $table = 'kategori';
    protected $fillable = ['nama', 'tipe', 'deskripsi'];

    public function transaksi() {
        return $this->hasMany(Transaksi::class);
    }

    public function anggaran() {
        return $this->hasMany(Anggaran::class);
    }
}