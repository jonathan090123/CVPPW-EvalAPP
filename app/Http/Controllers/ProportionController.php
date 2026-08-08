<?php

namespace App\Http\Controllers;

use App\Models\Criterion;
use App\Models\LevelProportion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class ProportionController extends Controller
{
    /**
     * Tampilkan form setting proporsi per jabatan per kriteria.
     */
    public function edit(): View
    {
        $criteria = Criterion::orderBy('id')->get();
        $positions = $this->managedPositions();
        $proportions = LevelProportion::all();

        // Bentuk map [position => [criterion_id => percentage bobot]]
        $map = [];
        foreach ($positions as $pos) {
            foreach ($criteria as $c) {
                $map[$pos][$c->id] = (float) $c->weight;
            }
        }
        foreach ($proportions as $p) {
            if (isset($map[$p->position][$p->criterion_id])) {
                $map[$p->position][$p->criterion_id] = (float) $p->proportion;
            }
        }

        return view('proportions.edit', compact('criteria', 'positions', 'map'));
    }

    /**
     * Simpan proporsi (upsert).
     */
    public function update(Request $request): RedirectResponse
    {
        $criteria = Criterion::orderBy('id')->get();
        $positions = $this->managedPositions();

        $rules = [];
        foreach ($positions as $pos) {
            foreach ($criteria as $c) {
                $rules["prop.{$pos}.{$c->id}"] = 'required|numeric|min:0|max:100';
            }
        }

        $validator = Validator::make($request->all(), $rules);
        $validator->after(function ($validator) use ($request, $positions, $criteria): void {
            foreach ($positions as $pos) {
                $total = 0.0;
                foreach ($criteria as $c) {
                    $total += (float) ($request->input("prop.{$pos}.{$c->id}", 0) ?? 0);
                }

                if (abs($total - 100) > 0.01) {
                    $validator->errors()->add("prop.{$pos}", "Total bobot untuk jabatan {$pos} harus 100%, saat ini {$total}%." );
                }
            }
        });

        $validated = $validator->validate();

        // Upsert satu per satu (data kecil, aman)
        foreach ($positions as $pos) {
            foreach ($criteria as $c) {
                $value = (float) $validated['prop'][$pos][$c->id];
                LevelProportion::updateOrCreate(
                    ['position' => $pos, 'criterion_id' => $c->id],
                    ['proportion' => $value],
                );
            }
        }

        return redirect()->route('proportions.edit')
            ->with('success', 'Proporsi penilaian per jabatan berhasil disimpan.');
    }

    /**
     * Jabatan yang dikelola proporsinya (sesuai hierarchy User, tanpa Owner).
     */
    private function managedPositions(): array
    {
        return array_values(array_filter(array_keys(\App\Models\User::HIERARCHY), fn ($p) => $p !== 'Owner'));
    }
}