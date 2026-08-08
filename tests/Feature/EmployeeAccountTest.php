<?php

use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('creates an account for non-staff employees using first-name credentials', function () {
    $employee = Employee::factory()->create([
        'nip' => '1001',
        'name' => 'KIKY YUDA OCTAVIANI',
        'department' => 'IT',
        'position' => 'KEPALA BAGIAN',
    ]);

    $employee->syncUserAccount();

    $user = User::where('username', 'kiky')->first();

    expect($user)->not->toBeNull()
        ->and($user->position)->toBe('KEPALA BAGIAN')
        ->and(Hash::check('kiky123', $user->password))->toBeTrue();
});

it('updates an existing uppercase username to the lowercase format', function () {
    $employee = Employee::withoutEvents(function () {
        return Employee::factory()->create([
            'nip' => '1004',
            'name' => 'INTAN PUTRI',
            'department' => 'HR',
            'position' => 'KOORDINATOR',
        ]);
    });

    $oldUser = User::create([
        'username' => 'INTAN',
        'position' => 'STAFF',
        'password' => 'oldpassword',
    ]);

    $employee->syncUserAccount();

    $updatedUser = User::find($oldUser->id);

    expect($updatedUser->username)->toBe('intan')
        ->and($updatedUser->position)->toBe('KOORDINATOR')
        ->and(Hash::check('intan123', $updatedUser->password))->toBeTrue();
});

it('does not create an account for staff employees', function () {
    $employee = Employee::factory()->create([
        'nip' => '1002',
        'name' => 'BUDI SANTOSO',
        'department' => 'IT',
        'position' => 'STAFF',
    ]);

    $employee->syncUserAccount();

    expect(User::where('username', 'BUDI')->exists())->toBeFalse();
});
