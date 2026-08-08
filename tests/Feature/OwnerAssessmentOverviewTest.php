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

