<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Hierarki jabatan: index rendah = level tinggi.
     * Owner = tertinggi (akses semua).
     */
    public const HIERARCHY = [
        'Owner' => 0,
        'KEPALA BAGIAN' => 1,
        'KOORDINATOR' => 2,
        'STAFF' => 3,
    ];

    protected $fillable = [
        'username',
        'password',
        'position',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function isOwner(): bool
    {
        return $this->position === 'Owner';
    }

    public function level(): int
    {
        return self::HIERARCHY[$this->position] ?? 99;
    }

    /**
     * Daftar jabatan (posisi karyawan) yang boleh diakses / dinilai user ini.
     */
    public function allowedEmployeePositions(): array
    {
        if ($this->isOwner()) {
            return array_keys(Employee::distinctPositions());
        }

        if ($this->position === 'KEPALA BAGIAN') {
            return ['KOORDINATOR', 'STAFF'];
        }

        if ($this->position === 'KOORDINATOR') {
            return ['STAFF'];
        }

        return [];
    }

    public function canManage(self $other): bool
    {
        if ($this->isOwner()) {
            return true;
        }

        return $this->level() < $other->level();
    }
}