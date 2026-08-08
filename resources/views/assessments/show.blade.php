@extends('layouts.app')
@section('title', 'Detail Penilaian')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('assessments.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h4 class="mb-0">Penilaian — {{ $assessment->name ?? $assessment->period }}
            @if ($assessment->name)
                <small class="text-muted ms-1">({{ $assessment->period }})</small>
            @endif
        </h4>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('ranking.show', $assessment) }}" class="btn btn-success btn-sm">
            <i class="bi bi-trophy me-1"></i>Lihat Ranking
        </a>
        <a href="{{ route('assessments.edit', $assessment) }}" class="btn btn-warning btn-sm">
            <i class="bi bi-pencil me-1"></i>Edit
        </a>
    </div>
</div>

@if ($assessment->description)
    <p class="text-muted mb-3">{{ $assessment->description }}</p>
@endif

<div class="card shadow-sm">
    <div class="card-header bg-white fw-semibold">Matriks Keputusan</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-sm mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="py-2 px-3">Karyawan</th>
                        @foreach ($criteria as $criterion)
                            <th class="py-2 px-2 text-center">
                                {{ $criterion->name }}
                                <br>
                                <span class="badge {{ $criterion->type === 'benefit' ? 'badge-benefit' : 'badge-cost' }} small">{{ $criterion->type }}</span>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($employees as $employee)
                        <tr>
                            <td class="py-2 px-3 fw-semibold">
                                {{ $employee->name }}
                                @if ($employee->position)
                                    @positionBadge($employee->position)
                                @endif
                                <br><small class="text-muted">{{ $employee->nip }} · {{ $employee->department ?? '-' }}</small>
                            </td>
                            @foreach ($criteria as $criterion)
                                @php
                                    $detail = $details->first(fn($d) => $d->employee_id === $employee->id && $d->criterion_id === $criterion->id);
                                @endphp
                                <td class="text-center">{{ $detail ? $detail->value : '-' }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
