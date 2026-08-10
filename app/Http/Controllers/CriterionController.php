<?php

namespace App\Http\Controllers;

use App\Models\Criterion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CriterionController extends Controller
{
    public function index(): View
    {
        $criteria = Criterion::orderBy('id')->get();

        return view('criteria.index', compact('criteria'));
    }

    public function create(): View
    {
        return view('criteria.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:benefit,cost',
        ]);

        Criterion::create($validated);

        return redirect()->route('criteria.index')
            ->with('success', 'Kriteria berhasil ditambahkan.');
    }

    public function edit(Criterion $criterion): View
    {
        return view('criteria.edit', compact('criterion'));
    }

    public function update(Request $request, Criterion $criterion): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:benefit,cost',
        ]);

        $criterion->update($validated);

        return redirect()->route('criteria.index')
            ->with('success', 'Kriteria berhasil diperbarui.');
    }

    public function destroy(Criterion $criterion): RedirectResponse
    {
        $criterion->delete();

        return redirect()->route('criteria.index')
            ->with('success', 'Kriteria berhasil dihapus.');
    }
}
