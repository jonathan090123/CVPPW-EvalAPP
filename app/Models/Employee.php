<?php

namespace App\Models;

use Database\Factories\EmployeeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    /** @use HasFactory<EmployeeFactory> */
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

        $username = $this->resolveUsername($firstName);
        $password = $username.'123';

        $existingUser = User::where('username', $username)
            ->first();

        $legacyUser = User::where('username', strtoupper($username))
            ->first();

        $lastNameUser = $this->findLegacyLastNameUser();

        $plainFirstNameUser = $this->findLegacyPlainFirstNameUser($username);

        $user = $existingUser;

        if (! $user && $legacyUser) {
            $user = $legacyUser;
        }

        if (! $user && $lastNameUser) {
            $user = $lastNameUser;
        }

        if (! $user && $plainFirstNameUser) {
            $user = $plainFirstNameUser;
        }

        if (! $user) {
            $user = new User;
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
     * Username = nama depan. Jika ada beberapa karyawan non-staff dengan
     * nama depan yang sama, diberi nomor urut berdasarkan id karyawan:
     * intan1, intan2, dst.
     */
    protected function resolveUsername(string $firstName): string
    {
        $base = strtolower($firstName);

        $siblings = static::query()
            ->where('position', '!=', 'STAFF')
            ->whereKeyNot($this->getKey())
            ->get(['id', 'name'])
            ->filter(fn (self $other) => strtolower((string) (preg_split('/\s+/', trim($other->name))[0] ?? '')) === $base)
            ->sortBy('id')
            ->values();

        if ($siblings->isEmpty()) {
            return $base;
        }

        $order = $siblings->pluck('id')->push($this->getKey())->sort()->values();

        $number = $order->search(fn ($id) => $id === $this->getKey()) + 1;

        return $base.$number;
    }

    /**
     * Cari akun lama yang masih memakai username nama belakang,
     * supaya migrasi ke nama depan tidak membuat akun ganda.
     */
    protected function findLegacyLastNameUser(): ?User
    {
        $name = trim((string) $this->name);
        if ($name === '') {
            return null;
        }

        $parts = preg_split('/\s+/', $name);
        $lastName = strtolower((string) end($parts));
        if ($lastName === '') {
            return null;
        }

        return User::whereIn('username', [$lastName, strtoupper($lastName)])
            ->first();
    }

    /**
     * Khusus karyawan pertama (nomor 1) dari nama ganda: klaim akun lama
     * yang masih memakai nama depan polos (mis. "intan") agar tidak ganda.
     */
    protected function findLegacyPlainFirstNameUser(string $username): ?User
    {
        if (! preg_match('/^(.+?)(\d+)$/', $username, $matches) || (int) $matches[2] !== 1) {
            return null;
        }

        return User::where('username', $matches[1])->first();
    }

    /**
     * Daftar unik jabatan (posisi) yang ada di tabel karyawan.
     *
     * @return array<string, string> value => value
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
