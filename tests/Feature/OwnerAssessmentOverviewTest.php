<?php

use App\Models\Assessment;
use App\Models\Criterion;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('allows owner to view assessment overview and accumulated ranking', function () {
    $owner = User::factory()->create(['position' => 'Owner']);

    $criterion = Criterion::factory()->create([
        'name' => 'Kualitas',
        'type' => 'benefit',
        'weight' => 100,
    ]);

    $employee = Employee::factory()->create([
        'nip' => '1003',
        'name' => 'Dina',
        'department' => 'MARKETING',
        'position' => 'KEPALA BAGIAN',
    ]);

    $assessment = Assessment::factory()->create([
        'name' => 'Penilaian Bulan Ini',
        'period' => '2026-08',
    ]);

    $assessment->details()->create([
        'employee_id' => $employee->id,
        'criterion_id' => $criterion->id,
        'value' => 5,
    ]);

    $response = $this->actingAs($owner)->get(route('assessments.ownerOverview'));

    $response->assertOk();
    $response->assertSee('Ringkasan Penilaian Owner');
    $response->assertSee('Penilaian Bulan Ini');
});

it('accumulates assessments created by all users and shows the average', function () {
    $owner = User::factory()->create(['position' => 'Owner']);
    $assessor = User::factory()->create(['username' => 'yessy', 'position' => 'KEPALA BAGIAN']);

    $criterion = Criterion::factory()->create([
        'name' => 'Kualitas',
        'type' => 'benefit',
        'weight' => 100,
    ]);

    $employee = Employee::factory()->create([
        'nip' => '1005',
        'name' => 'DINA KARYAWAN',
        'department' => 'MARKETING',
        'position' => 'STAFF',
    ]);

    $byOwner = Assessment::factory()->create([
        'name' => 'Penilaian Dari Owner',
        'period' => '2026-08',
        'created_by' => $owner->id,
    ]);
    $byOwner->details()->create([
        'employee_id' => $employee->id,
        'criterion_id' => $criterion->id,
        'value' => 5,
    ]);

    $byOther = Assessment::factory()->create([
        'name' => 'Penilaian Dari Kabag',
        'period' => '2026-08',
        'created_by' => $assessor->id,
    ]);
    $byOther->details()->create([
        'employee_id' => $employee->id,
        'criterion_id' => $criterion->id,
        'value' => 3,
    ]);

    $response = $this->actingAs($owner)->get(route('assessments.ownerOverview'));

    $response->assertOk();
    $response->assertSee('Penilaian Dari Owner');
    $response->assertSee('Penilaian Dari Kabag');
    $response->assertSee('Rata-rata');
    $response->assertSee('oleh yessy', escape: false);
});
