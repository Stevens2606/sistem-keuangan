<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Anggaran extends Model {
    protected $table = 'anggaran';
    protected $fillable = [
        'kategori_id',
        'jumlah',
        'periode_bulan',
        'periode_tahun',
        'keterangan',
        'created_by'
    ];

    protected $casts = [
        'jumlah' => 'decimal:2'
    ];

    public function kategori() {
        return $this->belongsTo(Kategori::class);
    }

    public function user() {
        return $this->belongsTo(User::class, 'created_by');
    }
}