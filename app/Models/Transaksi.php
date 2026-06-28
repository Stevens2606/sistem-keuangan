<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model {
    protected $table = 'transaksi';
    protected $fillable = [
        'kategori_id', 
        'tipe', 
        'jumlah', 
        'keterangan', 
        'tanggal', 
        'bukti_file', 
        'created_by'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah' => 'decimal:2'
    ];

    public function kategori() {
        return $this->belongsTo(Kategori::class);
    }

    public function user() {
        return $this->belongsTo(User::class, 'created_by');
    }
}