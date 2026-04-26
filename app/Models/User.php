<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'loket_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'loket_id'          => 'integer',
    ];

    // ── Role helpers ──────────────────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isOperator(): bool
    {
        return $this->role === 'operator';
    }

    /**
     * Apakah user boleh akses loket tertentu?
     * Admin boleh akses semua, operator hanya loket miliknya.
     */
    public function canAccessLoket(int $loketId): bool
    {
        if ($this->isAdmin()) return true;
        return $this->loket_id === $loketId;
    }

    public function loketInfo(): array
    {
        if ($this->isAdmin()) {
            return ['label' => 'Semua Loket', 'short' => 'Admin'];
        }
        return Queue::loketInfo($this->loket_id ?? 0);
    }
}
