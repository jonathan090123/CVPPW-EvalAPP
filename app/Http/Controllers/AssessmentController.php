<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AssessmentDetail;
use App\Models\Criterion;
use App\Models\Employee;
use App\Models\User;
use App\Services\McdmService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssessmentController extends Controller
{
    public function __construct(private McdmService $mcdmService) {}

    public function index(): View
    {
        $assessments = Assessment::query()
            ->when(auth()->check() && !auth()->user()->isOwner(), function ($query) {
                $query->where('created_by', auth()->id());
            })
            ->withCount('details')
            ->latest()
            ->paginate(15);

        return view('assessments.index', compact('assessments'));
    }

    public function create(): View
    {
        $allowed = auth()->user()->allowedEmployeePositions();
        $employees = Employee::orderBy('name')
            ->when($allowed, fn ($q) => $q->whereIn('position', $allowed))
            ->get();
        $criteria = Criterion::orderBy('id')->get();
        $departments = Employee::whereNotNull('department')->distinct()->pluck('department')->filter()->values();
        $positions = Employee::whereNotNull('position')->distinct()->pluck('position')
            ->filter()
            ->when($allowed, fn ($c) => $c->intersect($allowed))
            ->values();

        return view('assessments.create', compact('employees', 'criteria', 'departments', 'positions'));
    }

    public function storeEmployee(Request $request): JsonResponse
    {
        $allowed = auth()->user()->allowedEmployeePositions();
        $validated = $request->validate([
            'nip' => 'required|string|max:20|unique:employees,nip',
            'name' => 'required|string|max:100',
            'department' => 'required|string|max:100',
            'position' => ['required', 'string', 'max:100', in_array('*', $allowed) ? 'nullable' : 'in:'.implode(',', $allowed)],
        ]);

        $employee = Employee::create($validated);

        return response()->json([
            'employee' => $employee,
            'criteria' => Criterion::orderBy('id')->get(['id', 'name', 'type', 'weight']),
        ], 201);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'period' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'selected_employees' => 'required|array|min:1',
            'selected_employees.*' => 'required|integer|exists:employees,id',
            'scores' => 'required|array',
            'scores.*.*' => 'required|numeric|min:1|max:5',
        ]);

        // Pastikan user hanya menilai karyawan dengan jabatan yang boleh diaksesnya
        $allowed = auth()->user()->allowedEmployeePositions();
        $selectedIds = array_map('intval', $request->input('selected_employees', []));
        $validIds = Employee::whereIn('id', $selectedIds)
            ->when($allowed, fn ($q) => $q->whereIn('position', $allowed))
            ->pluck('id')
            ->all();

        if (empty($validIds)) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['selected_employees' => 'Anda tidak memiliki akses untuk menilai karyawan terpilih.']);
        }

        $assessment = Assessment::create([
            'name' => $request->name,
            'period' => $request->period,
            'description' => $request->description,
            'created_by' => auth()->id(),
        ]);

        $criteria = Criterion::orderBy('id')->get();

        foreach ($validIds as $employeeId) {
            foreach ($criteria as $criterion) {
                $value = $request->input("scores.{$employeeId}.{$criterion->id}");
                if ($value !== null) {
                    AssessmentDetail::create([
                        'assessment_id' => $assessment->id,
                        'employee_id' => $employeeId,
                        'criterion_id' => $criterion->id,
                        'value' => (float) $value,
                    ]);
                }
            }
        }

        return redirect()->route('assessments.show', $assessment)
            ->with('success', 'Penilaian berhasil disimpan.');
    }

    public function show(Assessment $assessment): View
    {
        $criteria = Criterion::orderBy('id')->get();
        $details = $assessment->details()->with(['employee', 'criterion'])->get();
        $employees = $details->pluck('employee')->unique('id')->values();

        return view('assessments.show', compact('assessment', 'criteria', 'details', 'employees'));
    }

    public function ownerOverview(Request $request): View
    {
        $selectedPosition = trim((string) $request->input('position', ''));
        $selectedDepartment = trim((string) $request->input('department', ''));
        $search = trim((string) $request->input('search', ''));

        $positions = Employee::whereNotNull('position')
            ->where('position', '!=', '')
            ->distinct()
            ->orderBy('position')
            ->pluck('position')
            ->filter()
            ->values();

        $departments = Employee::whereNotNull('department')
            ->where('department', '!=', '')
            ->distinct()
            ->orderBy('department')
            ->pluck('department')
            ->filter()
            ->values();

        $ownerId = User::where('position', 'Owner')->value('id');
        $ownerAssessments = $ownerId
            ? Assessment::query()
                ->where('created_by', $ownerId)
                ->withCount('details')
                ->latest()
                ->get()
            : collect();

        $assessments = $ownerAssessments->isNotEmpty()
            ? $ownerAssessments
            : Assessment::query()
                ->whereNull('created_by')
                ->withCount('details')
                ->latest()
                ->get();
        $criteria = Criterion::orderBy('id')->get();
        $overview = [];
        $accumulated = [];

        foreach ($assessments as $assessment) {
            $data = $this->mcdmService->buildMatrix($assessment);
            $positionsData = $data['positions'];
            $matrix = $data['matrix'];
            $scores = $this->mcdmService->calculateTopsis($matrix, $criteria, $positionsData, McdmService::loadProportions());
            $employees = $data['employees'];

            $ranking = [];
            $rank = 1;
            foreach ($scores as $employeeId => $score) {
                $employee = $employees->firstWhere('id', $employeeId);
                if (!$employee) {
                    continue;
                }

                if ($selectedPosition !== '' && $employee->position !== $selectedPosition) {
                    continue;
                }

                if ($selectedDepartment !== '' && $employee->department !== $selectedDepartment) {
                    continue;
                }

                if ($search !== '' && stripos($employee->name, $search) === false && stripos($employee->department, $search) === false && stripos($employee->position, $search) === false) {
                    continue;
                }

                $ranking[] = [
                    'rank' => $rank++,
                    'employee' => $employee,
                    'score' => $score,
                ];

                $accumulated[$employee->id] ??= [
                    'employee' => $employee,
                    'total' => 0.0,
                    'count' => 0,
                    'details' => [],
                ];
                $accumulated[$employee->id]['total'] += (float) $score;
                $accumulated[$employee->id]['count'] += 1;
                $accumulated[$employee->id]['details'][] = [
                    'assessment' => $assessment,
                    'rank' => $rank - 1,
                    'score' => (float) $score,
                ];
            }

            if (!empty($ranking)) {
                $overview[] = [
                    'assessment' => $assessment,
                    'criteria' => $criteria,
                    'ranking' => $ranking,
                ];
            }
        }

        usort($accumulated, function (array $a, array $b): int {
            return $b['total'] <=> $a['total'];
        });

        return view('assessments.owner_overview', compact('overview', 'positions', 'departments', 'selectedPosition', 'selectedDepartment', 'search', 'accumulated'));
    }

    public function edit(Assessment $assessment): View
    {
        $allowed = auth()->user()->allowedEmployeePositions();
        $employees = Employee::orderBy('name')
            ->when($allowed, fn ($q) => $q->whereIn('position', $allowed))
            ->get();
        $criteria = Criterion::orderBy('id')->get();
        $details = $assessment->details()->get()->keyBy(fn ($d) => "{$d->employee_id}_{$d->criterion_id}");

        return view('assessments.edit', compact('assessment', 'employees', 'criteria', 'details'));
    }

    public function updateInfo(Request $request, Assessment $assessment): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'period' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
        ]);

        $assessment->update([
            'name' => $request->name,
            'period' => $request->period,
            'description' => $request->description,
        ]);

        return redirect()->route('assessments.edit', $assessment)
            ->with('success_info', 'Informasi periode berhasil diperbarui.');
    }

    public function update(Request $request, Assessment $assessment): RedirectResponse
    {
        $request->validate([
            'scores' => 'required|array',
            'scores.*.*' => 'required|numeric|min:1|max:5',
        ]);

        $criteria = Criterion::orderBy('id')->get();
        $employees = Employee::orderBy('name')->get();

        foreach ($employees as $employee) {
            foreach ($criteria as $criterion) {
                $value = $request->input("scores.{$employee->id}.{$criterion->id}");
                if ($value !== null) {
                    AssessmentDetail::updateOrCreate(
                        [
                            'assessment_id' => $assessment->id,
                            'employee_id' => $employee->id,
                            'criterion_id' => $criterion->id,
                        ],
                        ['value' => (float) $value]
                    );
                }
            }
        }

        return redirect()->route('assessments.edit', $assessment)
            ->with('success_scores', 'Nilai karyawan berhasil diperbarui.');
    }

    public function destroy(Assessment $assessment): RedirectResponse
    {
        $assessment->delete();

        return redirect()->route('assessments.index')
            ->with('success', 'Penilaian berhasil dihapus.');
    }
}
