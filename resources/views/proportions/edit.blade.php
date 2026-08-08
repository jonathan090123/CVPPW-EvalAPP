@extends('layouts.app')
@section('title', 'Setting Proporsi Penilaian')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-tuning-knob me-2"></i>Setting Proporsi Penilaian per Jabatan</h4>
    <span class="badge bg-danger" style="font-size:.7rem">
        <i class="bi bi-lock me-1"></i>Hanya Owner
    </span>
</div>



<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('proportions.update') }}" method="POST">
            @csrf @method('PUT')

            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="py-2 px-3" style="min-width:160px">Jabatan</th>
                            @foreach ($criteria as $criterion)
                                <th class="py-2 px-2 text-center" style="min-width:110px">
                                    {{ $criterion->name }}
                                    <br>
                                    <span class="badge {{ $criterion->type === 'benefit' ? 'badge-benefit' : 'badge-cost' }} small">
                                        {{ $criterion->type }}
                                    </span>
                                    <br>
                                    <small class="text-light">%</small>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($positions as $pos)
                            @php
                                $totalWeight = 0;
                                foreach ($criteria as $c) {
                                    $totalWeight += $map[$pos][$c->id];
                                }
                            @endphp
                            <tr>
                                <td class="py-2 px-3 fw-semibold">
                                    @positionBadge($pos)
                                    <br>
                                    <small class="text-muted">Total bobot: <strong>{{ number_format($totalWeight, 2) }}%</strong></small>
                                    @if (abs($totalWeight - 100) > 0.01)
                                        <br>
                                        <small class="text-danger">Belum 100%</small>
                                    @else
                                        <br>
                                        <small class="text-success">Sesuai 100%</small>
                                    @endif
                                </td>
                                @foreach ($criteria as $criterion)
                                    <td class="p-1 text-center">
                                        <input type="number" step="0.01" min="0" max="100"
                                               name="prop[{{ $pos }}][{{ $criterion->id }}]"
                                               value="{{ rtrim(rtrim(number_format($map[$pos][$criterion->id], 2, '.', ''), '0'), '.') }}"
                                               class="form-control form-control-sm text-center"
                                               style="min-width:80px"
                                               placeholder="10%">
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex gap-2 mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Simpan Setting Bobot
                </button>
                <a href="{{ route('proportions.edit') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>Reset Form
                </a>
            </div>
        </form>
    </div>
</div>
@endsection