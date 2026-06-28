<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Anggaran extends Model
{
    protected $table = 'anggaran';

    protected $fillable = [
        'kategori_id',
        'jumlah',
        'periode_bulan',
        'periode_tahun',
        'keterangan',
        'created_by',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function realisasi()
    {
        return Transaksi::where('kategori_id', $this->kategori_id)
            ->whereMonth('tanggal', $this->periode_bulan)
            ->whereYear('tanggal', $this->periode_tahun)
            ->sum('jumlah');
    }

    public function persentase()
    {
        if ($this->jumlah == 0) return 0;
        return min(100, round(($this->realisasi() / $this->jumlah) * 100));
    }
}