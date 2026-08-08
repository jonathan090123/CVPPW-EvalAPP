<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Hash;

class Employee extends Model
{
    /** @use HasFactory<\Database\Factories\EmployeeFactory> */
    use HasFactory;

    protected $fillable = ['nip', 'name', 'department', 'position'];

    public function assessmentDetails(): HasMany
    {
        return $this->hasMany(AssessmentDetail::class);
    }

    protected static function booted(): void
    {
        static::saved(function (self $employee): void {
            $employee->syncUserAccount();
        });
    }

    public function syncUserAccount(): void
    {
        if ($this->position === 'STAFF') {
            return;
        }

        $firstName = $this->extractFirstName();
        if ($firstName === null) {
            return;
        }

        $username = strtolower($firstName);
        $password = strtolower($firstName).'123';

        $existingUser = \App\Models\User::where('username', $username)
            ->first();

        $legacyUser = \App\Models\User::where('username', strtoupper($username))
            ->first();

        $user = $existingUser;

        if (!$user && $legacyUser) {
            $user = $legacyUser;
        }

        if (!$user) {
            $user = new \App\Models\User();
        }

        $user->username = $username;
        $user->fill([
            'position' => $this->position,
            'password' => $password,
        ]);
        $user->save();
    }

    protected function extractFirstName(): ?string
    {
        $name = trim((string) $this->name);
        if ($name === '') {
            return null;
        }

        $parts = preg_split('/\s+/', $name);
        if (empty($parts)) {
            return null;
        }

        return $parts[0] ?: null;
    }

    /**
     * Daftar unik jabatan (posisi) yang ada di tabel karyawan.
     *
     * @return array<string, string>  value => value
     */
    public static function distinctPositions(): array
    {
        return self::query()
            ->whereNotNull('position')
            ->where('position', '!=', '')
            ->distinct()
            ->orderBy('position')
            ->pluck('position', 'position')
            ->all();
    }
}
