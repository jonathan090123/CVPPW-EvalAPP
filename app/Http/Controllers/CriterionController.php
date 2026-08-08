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
        $totalWeight = $criteria->sum('weight');

        return view('criteria.index', compact('criteria', 'totalWeight'));
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
            'weight' => 'required|numeric|min:0.01|max:100',
        ]);

        $currentTotal = Criterion::sum('weight');

        if ($currentTotal + $validated['weight'] > 100.01) {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'weight' => sprintf(
                        'Total bobot tidak boleh melebihi 100%%. Total saat ini %s%%, sisa bobot tersedia %s%%.',
                        number_format($currentTotal, 2),
                        number_format(max(0, 100 - $currentTotal), 2)
                    ),
                ]);
        }

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
            'weight' => 'required|numeric|min:0.01|max:100',
        ]);

        $othersTotal = Criterion::where('id', '!=', $criterion->id)->sum('weight');

        if ($othersTotal + $validated['weight'] > 100.01) {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'weight' => sprintf(
                        'Total bobot tidak boleh melebihi 100%%. Total bobot kriteria lain %s%%, sisa bobot tersedia %s%%.',
                        number_format($othersTotal, 2),
                        number_format(max(0, 100 - $othersTotal), 2)
                    ),
                ]);
        }

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
