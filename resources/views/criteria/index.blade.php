@extends('layouts.app')
@section('title', 'Daftar Kriteria')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-sliders me-2"></i>Kriteria Penilaian</h4>
    @if (auth()->user()->isOwner())
        <a href="{{ route('criteria.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>Tambah Kriteria
        </a>
    @else
        <span class="badge bg-info text-dark" style="font-size:.7rem">
            <i class="bi bi-lock me-1"></i>Hanya Owner yang dapat mengubah kriteria
        </span>
    @endif
</div>

@if (abs($totalWeight - 100) > 0.1)
    <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle me-1"></i>
        Total bobot saat ini <strong>{{ number_format($totalWeight, 2) }}%</strong>.
        Pastikan total bobot = <strong>100%</strong> agar perhitungan akurat.
    </div>
@else
    <div class="alert alert-success">
        <i class="bi bi-check-circle me-1"></i>
        Total bobot = <strong>100%</strong> &mdash; sudah valid.
    </div>
@endif

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th>Nama Kriteria</th>
                    <th>Jenis</th>
                    <th>Bobot</th>
                    <th width="15%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($criteria as $i => $criterion)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $criterion->name }}</td>
                        <td>
                            @if ($criterion->type === 'benefit')
                                <span class="badge badge-benefit">Benefit</span>
                            @else
                                <span class="badge badge-cost">Cost</span>
                            @endif
                        </td>
                        <td>{{ number_format($criterion->weight, 2) }}%</td>
                        <td>
                            @if (auth()->user()->isOwner())
                                <a href="{{ route('criteria.edit', $criterion) }}" class="btn btn-warning btn-sm">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('criteria.destroy', $criterion) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Hapus kriteria ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                                </form>
                            @else
                                <span class="text-muted small"><i class="bi bi-lock"></i></span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">Belum ada kriteria.</td></tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="table-light">
                    <td colspan="3" class="text-end fw-bold">Total Bobot</td>
                    <td class="fw-bold {{ abs($totalWeight - 100) > 0.1 ? 'text-danger' : 'text-success' }}">
                        {{ number_format($totalWeight, 2) }}%
                    </td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
