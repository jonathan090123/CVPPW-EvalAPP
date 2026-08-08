<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search', ''));
        $department = (string) $request->input('department', '');
        $position = (string) $request->input('position', '');

        $departments = Employee::whereNotNull('department')
            ->where('department', '!=', '')
            ->distinct()
            ->orderBy('department')
            ->pluck('department')
            ->values();
        $positions = array_keys(Employee::distinctPositions());

        $employees = Employee::query()
            ->when($search !== '', function ($q) use ($search) {
                $like = '%'.strtolower($search).'%';
                $q->where(function ($q) use ($like) {
                    $q->whereRaw('LOWER(name) LIKE ?', [$like])
                      ->orWhereRaw('LOWER(nip) LIKE ?', [$like]);
                });
            })
            ->when($department !== '', fn ($q) => $q->where('department', $department))
            ->when($position !== '', fn ($q) => $q->where('position', $position))
            ->orderBy('nip')
            ->paginate(50)
            ->appends($request->only('search', 'department', 'position'));

        return view('employees.index', compact('employees', 'departments', 'positions', 'search', 'department', 'position'));
    }

    public function create(): View
    {
        return view('employees.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nip' => 'required|string|max:20|unique:employees,nip',
            'name' => 'required|string|max:100',
            'department' => 'required|string|max:100',
            'position' => 'required|string|max:100',
        ]);

        Employee::create($validated);

        return redirect()->route('employees.index')
            ->with('success', 'Karyawan berhasil ditambahkan.');
    }

    public function show(Employee $employee): View
    {
        return view('employees.show', compact('employee'));
    }

    public function edit(Employee $employee): View
    {
        return view('employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee): RedirectResponse
    {
        $validated = $request->validate([
            'nip' => 'required|string|max:20|unique:employees,nip,'.$employee->id,
            'name' => 'required|string|max:100',
            'department' => 'required|string|max:100',
            'position' => 'required|string|max:100',
        ]);

        $employee->update($validated);

        return redirect()->route('employees.index')
            ->with('success', 'Data karyawan berhasil diperbarui.');
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        $employee->delete();

        return redirect()->route('employees.index')
            ->with('success', 'Karyawan berhasil dihapus.');
    }
}