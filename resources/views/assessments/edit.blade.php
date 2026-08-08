@extends('layouts.app')
@section('title', 'Edit Penilaian')

@section('content')
<div class="d-flex align-items-center mb-4 gap-2">
    <a href="{{ route('assessments.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h4 class="mb-0">Edit Penilaian — {{ $assessment->name ?? $assessment->period }}</h4>
</div>

@if(session('success_info'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success_info') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if(session('success_scores'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success_scores') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card shadow-sm mb-4" style="max-width:600px">
    <div class="card-header bg-white fw-semibold">Informasi Periode</div>
    <div class="card-body">
        <form action="{{ route('assessments.updateInfo', $assessment) }}" method="POST">
            @csrf @method('PATCH')
            <div class="mb-3">
                <label class="form-label fw-semibold">Nama Penilaian</label>
                <input type="text" name="name" value="{{ old('name', $assessment->name) }}"
                       class="form-control @error('name') is-invalid @enderror">
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Periode</label>
                <input type="text" name="period" value="{{ old('period', $assessment->period) }}"
                       class="form-control @error('period') is-invalid @enderror">
                @error('period')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Deskripsi</label>
                <textarea name="description" rows="2"
                          class="form-control">{{ old('description', $assessment->description) }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="bi bi-save me-1"></i>Simpan Informasi
            </button>
        </form>
    </div>
</div>

<form action="{{ route('assessments.update', $assessment) }}" method="POST">
@csrf @method('PUT')
<div class="card shadow-sm">
    <div class="card-header bg-white fw-semibold">Input Nilai Karyawan <small class="text-muted">(Skala 1–5)</small></div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-sm mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="py-2 px-3">Karyawan</th>
                        @foreach ($criteria as $criterion)
                            <th class="py-2 px-2 text-center" style="min-width:100px">
                                {{ $criterion->name }}
                                <br>
                                <span class="badge {{ $criterion->type === 'benefit' ? 'badge-benefit' : 'badge-cost' }} small">{{ $criterion->type }}</span>
                                <br>
                                <small class="text-light">w={{ number_format($criterion->weight, 2) }}%</small>
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
                                @php $key = "{$employee->id}_{$criterion->id}"; $existing = $details[$key]->value ?? 3; @endphp
                                <td class="p-1 text-center">
                                    <select name="scores[{{ $employee->id }}][{{ $criterion->id }}]"
                                            class="form-select form-select-sm" style="min-width:70px">
                                        @for ($v = 1; $v <= 5; $v++)
                                            <option value="{{ $v }}" {{ $existing == $v ? 'selected' : '' }}>{{ $v }}</option>
                                        @endfor
                                    </select>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white">
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save me-1"></i>Simpan Nilai
            </button>
            <a href="{{ route('assessments.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </div>
</div>
</form>
@endsection
