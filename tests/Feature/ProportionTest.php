<?php

use App\Models\Criterion;
use App\Models\LevelProportion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function proportionPayload(array $values): array
{
    $prop = [];
    foreach (['KEPALA BAGIAN', 'KOORDINATOR', 'STAFF'] as $pos) {
        foreach (Criterion::orderBy('id')->get() as $c) {
            $prop[$pos][$c->id] = $values[$pos][$c->id] ?? $values[$c->id] ?? 0;
        }
    }

    return ['prop' => $prop];
}

beforeEach(function () {
    Criterion::create(['name' => 'Kriteria A', 'type' => 'benefit', 'weight' => 60]);
    Criterion::create(['name' => 'Kriteria B', 'type' => 'benefit', 'weight' => 40]);

    $this->owner = User::create([
        'username' => 'owner',
        'position' => 'Owner',
        'password' => 'secret',
    ]);
});

it('saves integer proportions that total 100 per position', function () {
    $this->actingAs($this->owner)
        ->put(route('proportions.update'), proportionPayload([
            1 => 60,
            2 => 40,
        ]))
        ->assertRedirect(route('proportions.edit'))
        ->assertSessionHasNoErrors();

    expect((float) LevelProportion::where('position', 'STAFF')->where('criterion_id', 1)->value('proportion'))
        ->toBe(60.0);
});

it('rejects decimal (comma/float) proportion values', function () {
    $this->actingAs($this->owner)
        ->from(route('proportions.edit'))
        ->put(route('proportions.update'), proportionPayload([
            1 => 10.99,
            2 => 89.01,
        ]))
        ->assertSessionHasErrors();

    expect(LevelProportion::count())->toBe(0);
});

it('rejects proportions when a position total is not 100', function () {
    $this->actingAs($this->owner)
        ->from(route('proportions.edit'))
        ->put(route('proportions.update'), proportionPayload([
            'KEPALA BAGIAN' => [1 => 50, 2 => 40],
            'KOORDINATOR' => [1 => 60, 2 => 40],
            'STAFF' => [1 => 60, 2 => 40],
        ]))
        ->assertSessionHasErrors('prop.KEPALA BAGIAN');

    expect(LevelProportion::count())->toBe(0);
});
