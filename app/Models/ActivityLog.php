<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';

    protected $fillable = [
        'user_id', 'aksi', 'modul', 'deskripsi', 'ip_address',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function catat(string $aksi, string $modul, string $deskripsi): void
    {
        try {
            self::create([
                'user_id'    => auth()->id(),
                'aksi'       => $aksi,
                'modul'      => $modul,
                'deskripsi'  => $deskripsi,
                'ip_address' => request()->ip(),
            ]);
        } catch (\Exception $e) {
            \Log::error('ActivityLog gagal: ' . $e->getMessage());
        }
    }

    public function scopeModul(Builder $q, string $modul)   { return $q->where('modul', $modul); }
    public function scopeOlehUser(Builder $q, int $userId)  { return $q->where('user_id', $userId); }
    public function scopeTanggal(Builder $q, $dari, $sampai)
    {
        if ($dari)   $q->whereDate('created_at', '>=', $dari);
        if ($sampai) $q->whereDate('created_at', '<=', $sampai);
        return $q;
    }

    public function warnaBadge(): string
    {
        return match ($this->aksi) {
            'Tambah', 'Login' => 'success',
            'Edit'            => 'warning',
            'Hapus', 'Logout' => 'danger',
            default           => 'secondary',
        };
    }

    public function ikonAksi(): string
    {
        return match ($this->aksi) {
            'Tambah'         => 'bi-plus-circle',
            'Edit'           => 'bi-pencil',
            'Hapus'          => 'bi-trash',
            'Login'          => 'bi-box-arrow-in-right',
            'Logout'         => 'bi-box-arrow-right',
            'Ganti Password' => 'bi-key',
            'Edit Profil'    => 'bi-person',
            default          => 'bi-activity',
        };
    }
}