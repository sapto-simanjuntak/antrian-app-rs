<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'user_name',
        'loket_id',
        'action',
        'kode_antrian',
        'notes',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public static function record(
        int $loketId,
        string $action,
        ?string $kodeAntrian = null,
        ?string $notes = null
    ): void {
        $user = auth()->user();

        static::create([
            'user_id'      => $user?->id,
            'user_name'    => $user?->name ?? 'System',
            'loket_id'     => $loketId,
            'action'       => $action,
            'kode_antrian' => $kodeAntrian,
            'notes'        => $notes,
        ]);
    }

    public static function actionLabel(string $action): string
    {
        return match ($action) {
            'panggil'       => 'Memanggil',
            'panggil_ulang' => 'Panggil Ulang',
            'pause'         => 'Ditunda',
            'selesai'       => 'Selesai',
            'batal'         => 'Dibatalkan',
            'tidak_hadir'   => 'Tidak Hadir',
            'ambil'         => 'Ambil Nomor',
            default         => ucfirst($action),
        };
    }
}
