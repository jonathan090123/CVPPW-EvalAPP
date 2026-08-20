<?php

use App\Models\Assessment;
use App\Models\AssessmentDetail;
use App\Models\Criterion;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    Criterion::create(['name' => 'Kinerja', 'type' => 'benefit', 'weight' => 100]);

    // KOORDINATOR Marketing -> otomatis membuat user login "kiki" (nama depan)
    Employee::create(['nip' => '001', 'name' => 'KIKI KOORD', 'department' => 'Marketing', 'position' => 'KOORDINATOR']);
    $this->marketingStaff = Employee::create(['nip' => '002', 'name' => 'BUDI SANTOSO', 'department' => 'Marketing', 'position' => 'STAFF']);
    $this->hrdStaff = Employee::create(['nip' => '003', 'name' => 'ANDI WIJAYA', 'department' => 'HRD', 'position' => 'STAFF']);

    $this->kiki = User::where('username', 'kiki')->firstOrFail();
    $this->owner = User::create(['username' => 'owner', 'position' => 'Owner', 'password' => 'secret']);
});

it('shows only same-department employees on create for non-owner users', function () {
    $this->actingAs($this->kiki)
        ->get(route('assessments.create'))
        ->assertOk()
        ->assertSee('BUDI SANTOSO')
        ->assertDontSee('ANDI WIJAYA')
        ->assertSee('Marketing')
        ->assertDontSee('HRD');
});

it('shows all departments on create for owner', function () {
    $this->actingAs($this->owner)
        ->get(route('assessments.create'))
        ->assertOk()
        ->assertSee('BUDI SANTOSO')
        ->assertSee('ANDI WIJAYA');
});

it('shows only same-department employees on edit for non-owner users', function () {
    $assessment = Assessment::create([
        'name' => 'Penilaian Marketing',
        'period' => '2026',
        'created_by' => $this->kiki->id,
    ]);

    $this->actingAs($this->kiki)
        ->get(route('assessments.edit', $assessment))
        ->assertOk()
        ->assertSee('BUDI SANTOSO')
        ->assertDontSee('ANDI WIJAYA')
        ->assertSee('Marketing')
        ->assertDontSee('HRD');
});

it('shows all departments on edit for owner', function () {
    $assessment = Assessment::create([
        'name' => 'Penilaian Semua',
        'period' => '2026',
        'created_by' => $this->owner->id,
    ]);

    $this->actingAs($this->owner)
        ->get(route('assessments.edit', $assessment))
        ->assertOk()
        ->assertSee('BUDI SANTOSO')
        ->assertSee('ANDI WIJAYA');
});

it('only updates details for same-department employees when non-owner submits edit', function () {
    $criterion = Criterion::first();
    $assessment = Assessment::create([
        'name' => 'Penilaian Marketing',
        'period' => '2026',
        'created_by' => $this->kiki->id,
    ]);

    $this->actingAs($this->kiki)
        ->put(route('assessments.update', $assessment), [
            'scores' => [
                $this->marketingStaff->id => [$criterion->id => 4],
                $this->hrdStaff->id => [$criterion->id => 5],
            ],
        ])
        ->assertSessionHasNoErrors();

    $employeeIds = AssessmentDetail::where('assessment_id', $assessment->id)->pluck('employee_id')->all();

    expect($employeeIds)->toContain($this->marketingStaff->id)
        ->not->toContain($this->hrdStaff->id);
});

it('only stores details for same-department employees when non-owner submits', function () {
    $criterion = Criterion::first();

    $this->actingAs($this->kiki)
        ->post(route('assessments.store'), [
            'name' => 'Penilaian Marketing',
            'period' => '2026',
            'selected_employees' => [$this->marketingStaff->id, $this->hrdStaff->id],
            'scores' => [
                $this->marketingStaff->id => [$criterion->id => 4],
                $this->hrdStaff->id => [$criterion->id => 5],
            ],
        ])
        ->assertSessionHasNoErrors();

    $assessment = Assessment::firstOrFail();
    $employeeIds = AssessmentDetail::where('assessment_id', $assessment->id)->pluck('employee_id')->all();

    expect($employeeIds)->toContain($this->marketingStaff->id)
        ->not->toContain($this->hrdStaff->id);
});

it('rejects submission when non-owner selects only other-department employees', function () {
    $criterion = Criterion::first();

    $this->actingAs($this->kiki)
        ->from(route('assessments.create'))
        ->post(route('assessments.store'), [
            'name' => 'Penilaian HRD',
            'period' => '2026',
            'selected_employees' => [$this->hrdStaff->id],
            'scores' => [
                $this->hrdStaff->id => [$criterion->id => 5],
            ],
        ])
        ->assertSessionHasErrors('selected_employees');

    expect(Assessment::count())->toBe(0);
});

it('lets owner store assessments for any department', function () {
    $criterion = Criterion::first();

    $this->actingAs($this->owner)
        ->post(route('assessments.store'), [
            'name' => 'Penilaian Semua',
            'period' => '2026',
            'selected_employees' => [$this->marketingStaff->id, $this->hrdStaff->id],
            'scores' => [
                $this->marketingStaff->id => [$criterion->id => 4],
                $this->hrdStaff->id => [$criterion->id => 5],
            ],
        ])
        ->assertSessionHasNoErrors();

    $assessment = Assessment::firstOrFail();
    $employeeIds = AssessmentDetail::where('assessment_id', $assessment->id)->pluck('employee_id')->all();

    expect($employeeIds)->toContain($this->marketingStaff->id)
        ->toContain($this->hrdStaff->id);
});

it('forces quick-added employees into the non-owner user department', function () {
    $this->actingAs($this->kiki)
        ->postJson(route('assessments.storeEmployee'), [
            'nip' => '009',
            'name' => 'SITI BARU',
            'department' => 'HRD',
            'position' => 'STAFF',
        ])
        ->assertCreated();

    expect(Employee::where('nip', '009')->value('department'))->toBe('Marketing');
});
