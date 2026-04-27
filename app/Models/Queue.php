<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Queue extends Model
{
    protected $table = 'queues';

    protected $fillable = [
        'loket_id',
        'nomor_antrian',
        'kode_antrian',
        'status',
        'called_at',
        'done_at',
        'service_duration',
    ];

    protected $casts = [
        'called_at' => 'datetime',
        'done_at'   => 'datetime',
    ];

    // ── Constants ──────────────────────────────────────────────────────────────

    public const LOKETS = [
        1 => ['label' => 'BPJS Kesehatan', 'short' => 'BPJS', 'prefix' => 'B', 'icon' => 'shield-check'],
        2 => ['label' => 'Pasien Umum',    'short' => 'Umum', 'prefix' => 'U', 'icon' => 'person-badge'],
        3 => ['label' => 'Pasien Lansia',  'short' => 'Lansia', 'prefix' => 'L', 'icon' => 'person-heart'],
    ];

    // 'serving' dihapus — timer langsung jalan dari called_at
    public const ACTIVE_STATUSES = ['calling', 'paused'];

    // ── Scopes ─────────────────────────────────────────────────────────────────

    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeLoket(Builder $query, int $loketId): Builder
    {
        return $query->where('loket_id', $loketId);
    }

    public function scopeWaiting(Builder $query): Builder
    {
        return $query->where('status', 'waiting');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', self::ACTIVE_STATUSES);
    }

    public function scopeDone(Builder $query): Builder
    {
        return $query->where('status', 'done');
    }

    // ── Accessors ──────────────────────────────────────────────────────────────

    protected function formattedNumber(): Attribute
    {
        return Attribute::make(
            get: fn() => str_pad($this->nomor_antrian, 3, '0', STR_PAD_LEFT)
        );
    }

    protected function durationHuman(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! $this->service_duration) return '-';
                $m = intdiv($this->service_duration, 60);
                $s = $this->service_duration % 60;
                return $m > 0 ? "{$m} mnt {$s} dtk" : "{$s} dtk";
            }
        );
    }

    protected function statusLabel(): Attribute
    {
        return Attribute::make(
            get: fn() => match ($this->status) {
                'waiting'   => 'Menunggu',
                'calling'   => 'Dipanggil',
                'paused'    => 'Ditunda',
                'done'      => 'Selesai',
                'cancelled' => 'Batal',
                'no_show'   => 'Tidak Hadir',
                default     => ucfirst($this->status),
            }
        );
    }

    // ── Static helpers ─────────────────────────────────────────────────────────

    /**
     * Generate next antrian number safely (race-condition proof via DB lock).
     * Call this inside a DB::transaction().
     */
    public static function nextNumber(int $loketId): int
    {
        $last = static::today()
            ->loket($loketId)
            ->lockForUpdate()
            ->max('nomor_antrian');

        return ($last ?? 0) + 1;
    }

    public static function loketInfo(int $loketId): array
    {
        return self::LOKETS[$loketId] ?? ['label' => 'Unknown', 'short' => '?', 'prefix' => 'X', 'icon' => 'question'];
    }

    public static function generateKode(int $loketId, int $nomor): string
    {
        $prefix = self::LOKETS[$loketId]['prefix'] ?? 'X';
        return $prefix . str_pad($nomor, 3, '0', STR_PAD_LEFT);
    }

    // ── Serialization ──────────────────────────────────────────────────────────

    public function toApiArray(): array
    {
        return [
            'id'               => $this->id,
            'loket_id'         => $this->loket_id,
            'loket_info'       => self::loketInfo($this->loket_id),
            'nomor_antrian'    => $this->nomor_antrian,
            'kode_antrian'     => $this->kode_antrian,
            'formatted'        => $this->formatted_number,
            'status'           => $this->status,
            'status_label'     => $this->status_label,
            'called_at'        => $this->called_at?->format('H:i:s'),
            'done_at'          => $this->done_at?->format('H:i:s'),
            'service_duration' => $this->service_duration,
            'duration_human'   => $this->duration_human,
            'created_at'       => $this->created_at?->format('d/m/Y H:i:s'),
        ];
    }
}
