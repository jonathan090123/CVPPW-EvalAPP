<?php

use App\Services\McdmService;

it('uses per-position percentage weights for TOPSIS scoring', function () {
    $service = new McdmService();

    $criteria = collect([
        (object) ['id' => 1, 'name' => 'Kualitas', 'type' => 'benefit', 'weight' => 90],
        (object) ['id' => 2, 'name' => 'Produktivitas', 'type' => 'benefit', 'weight' => 10],
    ]);

    $matrix = [
        1 => [1 => 10, 2 => 0],
        2 => [1 => 0, 2 => 10],
    ];

    $positions = [
        1 => 'KEPALA BAGIAN',
        2 => 'KEPALA BAGIAN',
    ];

    $proportions = [
        'KEPALA BAGIAN' => [1 => 20, 2 => 80],
    ];

    $scores = $service->calculateTopsis($matrix, $criteria, $positions, $proportions);

    expect(array_keys($scores)[0])->toBe(2);
});
