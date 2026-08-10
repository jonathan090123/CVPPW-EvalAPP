<?php

use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('shows the settings page to authenticated users', function () {
    $user = User::create([
        'username' => 'budi',
        'position' => 'STAFF',
        'password' => 'secret',
    ]);

    $this->actingAs($user)
        ->get(route('settings.edit'))
        ->assertOk()
        ->assertSee('Pengaturan Akun');
});

it('redirects guests to login', function () {
    $this->get(route('settings.edit'))->assertRedirect(route('login'));
});

it('shows position and department for employee-linked users', function () {
    Employee::factory()->create([
        'nip' => '1001',
        'name' => 'KIKY YUDA OCTAVIANI',
        'department' => 'IT',
        'position' => 'KEPALA BAGIAN',
    ]);

    $user = User::where('username', 'kiky')->firstOrFail();

    $this->actingAs($user)
        ->get(route('settings.edit'))
        ->assertOk()
        ->assertSee('KEPALA BAGIAN')
        ->assertSee('IT');
});

it('shows Owner for both position and department for owner users', function () {
    $user = User::create([
        'username' => 'owner',
        'position' => 'Owner',
        'password' => 'secret',
    ]);

    $this->actingAs($user)
        ->get(route('settings.edit'))
        ->assertOk()
        ->assertSee('value="Owner"', escape: false);
});

it('updates the password when the current password is correct', function () {
    $user = User::create([
        'username' => 'budi',
        'position' => 'STAFF',
        'password' => 'secret',
    ]);

    $this->actingAs($user)
        ->put(route('settings.update'), [
            'current_password' => 'secret',
            'password' => 'password_baru',
            'password_confirmation' => 'password_baru',
        ])
        ->assertRedirect(route('settings.edit'));

    expect(Hash::check('password_baru', $user->fresh()->password))->toBeTrue();
});

it('rejects a password change when the current password is wrong', function () {
    $user = User::create([
        'username' => 'budi',
        'position' => 'STAFF',
        'password' => 'secret',
    ]);

    $this->actingAs($user)
        ->from(route('settings.edit'))
        ->put(route('settings.update'), [
            'current_password' => 'salah',
            'password' => 'password_baru',
            'password_confirmation' => 'password_baru',
        ])
        ->assertSessionHasErrors('current_password');

    expect(Hash::check('secret', $user->fresh()->password))->toBeTrue();
});

it('rejects a password change when confirmation does not match', function () {
    $user = User::create([
        'username' => 'budi',
        'position' => 'STAFF',
        'password' => 'secret',
    ]);

    $this->actingAs($user)
        ->from(route('settings.edit'))
        ->put(route('settings.update'), [
            'current_password' => 'secret',
            'password' => 'password_baru',
            'password_confirmation' => 'beda',
        ])
        ->assertSessionHasErrors('password');

    expect(Hash::check('secret', $user->fresh()->password))->toBeTrue();
});
