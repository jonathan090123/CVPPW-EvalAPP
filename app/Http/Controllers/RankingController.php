<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\Employee;
use App\Services\McdmService;
use Illuminate\View\View;

class RankingController extends Controller
{
    public function __construct(private McdmService $mcdmService) {}

    public function show(Assessment $assessment): View
    {
        $data = $this->mcdmService->buildMatrix($assessment);

        $matrix = $data['matrix'];
        $employees = $data['employees'];
        $criteria = $data['criteria'];
        $positions = $data['positions'];

        $proportions = McdmService::loadProportions();
        $scores = $this->mcdmService->calculateTopsis($matrix, $criteria, $positions, $proportions);
        $ranking = $this->buildRankingList($scores, $employees);

        return view('ranking.show', compact(
            'assessment',
            'criteria',
            'employees',
            'matrix',
            'scores',
            'ranking',
        ));
    }

    /**
     * @param  array<int, float>  $scores
     * @return array<int, array{rank: int, employee: Employee, score: float}>
     */
    private function buildRankingList(array $scores, $employees): array
    {
        $employeeMap = $employees->keyBy('id');
        $ranking = [];
        $rank = 1;
        foreach ($scores as $employeeId => $score) {
            $ranking[] = [
                'rank' => $rank++,
                'employee' => $employeeMap[$employeeId],
                'score' => $score,
            ];
        }

        return $ranking;
    }
}