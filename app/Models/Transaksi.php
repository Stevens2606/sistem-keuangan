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
        'created_by',
        'status',
        'catatan_approval',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'tanggal'     => 'date',
        'jumlah'      => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public function kategori() {
        return $this->belongsTo(Kategori::class);
    }

    public function user() {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver() {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope: hanya transaksi yang sudah disetujui.
     * WAJIB dipakai di semua kalkulasi saldo, anggaran, dan dashboard,
     * supaya transaksi yang masih 'menunggu' tidak ikut terhitung.
     */
    public function scopeApproved($query) {
        return $query->where('status', 'disetujui');
    }

    public function scopeMenunggu($query) {
        return $query->where('status', 'menunggu');
    }

    public function scopeDitolak($query) {
        return $query->where('status', 'ditolak');
    }

    /**
     * Label status untuk badge di Blade.
     */
    public function labelStatus(): string {
        return match($this->status) {
            'disetujui' => 'Disetujui',
            'menunggu'  => 'Menunggu Persetujuan',
            'ditolak'   => 'Ditolak',
            default     => $this->status,
        };
    }
}