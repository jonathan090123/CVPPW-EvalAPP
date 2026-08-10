@extends('layouts.app')
@section('title', 'Hasil Ranking — ' . ($assessment->name ?? $assessment->period))

@section('content')
<div class="d-flex align-items-center mb-4 gap-2">
    <a href="{{ route('assessments.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h4 class="mb-0"><i class="bi bi-trophy me-2"></i>Hasil Ranking — {{ $assessment->name ?? $assessment->period }}
        @if ($assessment->name)
            <small class="text-muted ms-1">({{ $assessment->period }})</small>
        @endif
    </h4>
    <form action="{{ route('assessments.destroy', $assessment) }}" method="POST"
          class="ms-auto"
          onsubmit="return confirm('Hapus penilaian & hasil ranking ini?')">
        @csrf @method('DELETE')
        <button class="btn btn-danger btn-sm" title="Hapus">
            <i class="bi bi-trash me-1"></i>Hapus Sesi
        </button>
    </form>
</div>

{{-- Criteria info --}}
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white fw-semibold">Kriteria Penilaian</div>
    <div class="card-body p-0">
        <table class="table table-sm mb-0">
            <thead class="table-light">
                <tr>
                    <th>Kriteria</th>
                    <th>Jenis</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($criteria as $criterion)
                    <tr>
                        <td>{{ $criterion->name }}</td>
                        <td>
                            <span class="badge {{ $criterion->type === 'benefit' ? 'badge-benefit' : 'badge-cost' }}">
                                {{ ucfirst($criterion->type) }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Decision matrix --}}
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white fw-semibold">Matriks Keputusan (Nilai Asli)</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-sm mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Karyawan</th>
                        @foreach ($criteria as $criterion)
                            <th class="text-center">{{ $criterion->name }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($employees as $employee)
                        <tr>
                            <td class="fw-semibold">{{ $employee->name }}</td>
                            @foreach ($criteria as $criterion)
                                <td class="text-center">{{ number_format($matrix[$employee->id][$criterion->id] ?? 0, 1) }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Hasil Ranking --}}
<div class="card shadow-sm">
    <div class="card-header bg-primary text-white fw-semibold">
        <i class="bi bi-trophy me-1"></i>Hasil Ranking
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="10%">Rank</th>
                        <th>Karyawan</th>
                        <th>NIP</th>
                        <th>Skor</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($ranking as $item)
                        <tr class="{{ $item['rank'] === 1 ? 'table-warning' : '' }}">
                            <td class="fw-bold text-center">
                                @if ($item['rank'] === 1)
                                    <i class="bi bi-trophy-fill text-warning"></i>
                                @else
                                    {{ $item['rank'] }}
                                @endif
                            </td>
                            <td>
                                {{ $item['employee']->name }}
                                @if ($item['employee']->position)
                                    @positionBadge($item['employee']->position)
                                @endif
                            </td>
                            <td class="text-muted small">{{ $item['employee']->nip }}</td>
                            <td>
                                <span class="fw-semibold">{{ number_format($item['score'], 4) }}</span>
                                <div class="progress mt-1" style="height:4px">
                                    <div class="progress-bar bg-primary"
                                         style="width:{{ $item['score'] * 100 }}%"></div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection