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

    /**
     * Departemen user, dicocokkan dari data karyawan melalui username (nama depan).
     * Username bernomor (intan1, intan2, ...) menunjuk karyawan ke-n dengan
     * nama depan sama (urut id, non-staff). Owner tidak terikat departemen.
     */
    public function department(): ?string
    {
        if ($this->isOwner()) {
            return 'Owner';
        }

        $username = strtolower($this->username);
        $base = $username;
        $number = null;

        if (preg_match('/^(.+?)(\d+)$/', $username, $matches)) {
            $base = $matches[1];
            $number = (int) $matches[2];
        }

        $matchesBase = fn (Employee $employee) => strtolower((string) strtok(trim($employee->name), ' ')) === $base;

        $candidates = Employee::query()
            ->whereNotNull('department')
            ->where('position', '!=', 'STAFF')
            ->get(['id', 'name', 'department'])
            ->filter($matchesBase)
            ->sortBy('id')
            ->values();

        if ($candidates->isEmpty()) {
            $candidates = Employee::query()
                ->whereNotNull('department')
                ->get(['id', 'name', 'department'])
                ->filter($matchesBase)
                ->sortBy('id')
                ->values();
        }

        if ($number !== null) {
            return $candidates->get($number - 1)?->department;
        }

        return $candidates->first()?->department;
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
