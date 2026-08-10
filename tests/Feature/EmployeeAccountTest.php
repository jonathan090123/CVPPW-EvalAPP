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

it('migrates an existing last-name username to the first-name format', function () {
    $employee = Employee::withoutEvents(function () {
        return Employee::factory()->create([
            'nip' => '1004',
            'name' => 'INTAN PUTRI',
            'department' => 'HR',
            'position' => 'KOORDINATOR',
        ]);
    });

    $oldUser = User::create([
        'username' => 'putri',
        'position' => 'STAFF',
        'password' => 'oldpassword',
    ]);

    $employee->syncUserAccount();

    $updatedUser = User::find($oldUser->id);

    expect($updatedUser->username)->toBe('intan')
        ->and($updatedUser->position)->toBe('KOORDINATOR')
        ->and(Hash::check('intan123', $updatedUser->password))->toBeTrue();
});

it('numbers usernames when non-staff employees share a first name', function () {
    $first = Employee::factory()->create([
        'nip' => '1010',
        'name' => 'INTAN LUTFI',
        'department' => 'HR',
        'position' => 'KOORDINATOR',
    ]);

    $second = Employee::factory()->create([
        'nip' => '1011',
        'name' => 'INTAN PUTRI',
        'department' => 'IT',
        'position' => 'KOORDINATOR',
    ]);

    $first->syncUserAccount();
    $second->syncUserAccount();

    $userOne = User::where('username', 'intan1')->first();
    $userTwo = User::where('username', 'intan2')->first();

    expect($userOne)->not->toBeNull()
        ->and(Hash::check('intan1123', $userOne->password))->toBeTrue()
        ->and($userTwo)->not->toBeNull()
        ->and(Hash::check('intan2123', $userTwo->password))->toBeTrue();
});

it('ignores staff employees when numbering duplicate first names', function () {
    Employee::factory()->create([
        'nip' => '1020',
        'name' => 'INTAN STAFF',
        'department' => 'HR',
        'position' => 'STAFF',
    ]);

    $coordinator = Employee::factory()->create([
        'nip' => '1021',
        'name' => 'INTAN PUTRI',
        'department' => 'IT',
        'position' => 'KOORDINATOR',
    ]);

    $coordinator->syncUserAccount();

    expect(User::where('username', 'intan')->exists())->toBeTrue()
        ->and(User::where('username', 'intan1')->exists())->toBeFalse();
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
