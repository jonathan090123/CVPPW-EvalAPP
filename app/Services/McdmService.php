<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\Criterion;
use App\Models\LevelProportion;
use Illuminate\Support\Collection;

class McdmService
{
    public function __construct() {}

    /**
     * Build a matrix [employee_id => [criterion_id => value]] from an assessment.
     *
     * @return array{matrix: array<int, array<int, float>>, employees: Collection, criteria: Collection, positions: array<int, string>}
     */
    public function buildMatrix(Assessment $assessment): array
    {
        $details = $assessment->details()->with(['employee', 'criterion'])->get();
        $criteria = Criterion::orderBy('id')->get();
        $employees = $assessment->details()->with('employee')
            ->get()
            ->pluck('employee')
            ->unique('id')
            ->values();

        $matrix = [];
        $positions = [];
        foreach ($employees as $employee) {
            $positions[$employee->id] = $employee->position ?? 'STAFF';
            foreach ($criteria as $criterion) {
                $detail = $details->first(
                    fn ($d) => $d->employee_id === $employee->id && $d->criterion_id === $criterion->id
                );
                $matrix[$employee->id][$criterion->id] = $detail ? (float) $detail->value : 0.0;
            }
        }

        return compact('matrix', 'employees', 'criteria', 'positions');
    }

    /**
     * TOPSIS calculation with optional per-position percentage weighting.
     *
     * @param  array<int, array<int, float>>  $matrix
     * @param  array<int, string>|null  $positions  employee_id => position (untuk bobot per jabatan)
     * @param  array<string, array<int, float>>|null  $proportions  position => [criterion_id => percentage]
     * @return array<int, float>  employee_id => preference score (closeness)
     */
    public function calculateTopsis(array $matrix, Collection $criteria, ?array $positions = null, ?array $proportions = null): array
    {
        // Step 1: Normalize (vector normalization)
        $vectorNorm = [];
        foreach ($criteria as $criterion) {
            $sumSquares = 0.0;
            foreach ($matrix as $row) {
                $sumSquares += ($row[$criterion->id] ?? 0.0) ** 2;
            }
            $vectorNorm[$criterion->id] = sqrt($sumSquares);
        }

        $normalized = [];
        foreach ($matrix as $employeeId => $row) {
            foreach ($criteria as $criterion) {
                $denom = $vectorNorm[$criterion->id];
                $normalized[$employeeId][$criterion->id] = $denom > 0
                    ? ($row[$criterion->id] ?? 0.0) / $denom
                    : 0.0;
            }
        }

        // Step 2: Weighted normalized matrix (dengan bobot per-jabatan jika tersedia)
        $weighted = [];
        foreach ($matrix as $employeeId => $_) {
            $pos = $positions[$employeeId] ?? null;
            $positionWeights = [];

            foreach ($criteria as $criterion) {
                $positionWeights[$criterion->id] = ($pos !== null && isset($proportions[$pos][$criterion->id]))
                    ? (float) $proportions[$pos][$criterion->id]
                    : (float) $criterion->weight;
            }

            $totalPositionWeight = array_sum($positionWeights);
            if ($totalPositionWeight <= 0) {
                $totalPositionWeight = 1.0;
            }

            foreach ($criteria as $criterion) {
                $relativeWeight = $positionWeights[$criterion->id] / $totalPositionWeight;
                $weighted[$employeeId][$criterion->id] =
                    $relativeWeight * ($normalized[$employeeId][$criterion->id] ?? 0.0);
            }
        }

        // Step 3: Ideal positive (A+) and ideal negative (A-)
        $positiveIdeal = [];
        $negativeIdeal = [];
        foreach ($criteria as $criterion) {
            $values = [];
            foreach ($weighted as $row) {
                $values[] = $row[$criterion->id] ?? 0.0;
            }

            if ($criterion->type === 'benefit') {
                $positiveIdeal[$criterion->id] = max($values) ?: 0.0;
                $negativeIdeal[$criterion->id] = min($values) ?: 0.0;
            } else {
                // cost: lower is better → positive ideal = min
                $positiveIdeal[$criterion->id] = min($values) ?: 0.0;
                $negativeIdeal[$criterion->id] = max($values) ?: 0.0;
            }
        }

        // Step 4: Distance to ideal positive and negative
        $scores = [];
        foreach ($matrix as $employeeId => $_) {
            $dPlus = 0.0;
            $dMinus = 0.0;
            foreach ($criteria as $criterion) {
                $v = $weighted[$employeeId][$criterion->id] ?? 0.0;
                $dPlus += ($v - $positiveIdeal[$criterion->id]) ** 2;
                $dMinus += ($v - $negativeIdeal[$criterion->id]) ** 2;
            }
            $dPlus = sqrt($dPlus);
            $dMinus = sqrt($dMinus);

            // Step 5: Relative closeness to ideal solution
            $scores[$employeeId] = ($dPlus + $dMinus) > 0
                ? $dMinus / ($dPlus + $dMinus)
                : 0.0;
        }

        arsort($scores);

        return $scores;
    }

    /**
     * Load peta bobot [position => [criterion_id => percentage]] dari DB.
     */
    public static function loadProportions(): array
    {
        return LevelProportion::loadAll();
    }
}