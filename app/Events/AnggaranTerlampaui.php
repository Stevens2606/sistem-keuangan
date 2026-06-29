<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AnggaranTerlampaui implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $data;

    /**
     * @param array $data Hasil dari AnggaranNotifikasiService::cekSatuKategori()
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Channel privat — hanya admin & bendahara yang boleh subscribe
     * (otorisasi diatur di routes/channels.php).
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('notifikasi.keuangan'),
        ];
    }

    /**
     * Nama event di sisi JS: .anggaran.notifikasi
     */
    public function broadcastAs(): string
    {
        return 'anggaran.notifikasi';
    }

    public function broadcastWith(): array
    {
        return $this->data;
    }
}