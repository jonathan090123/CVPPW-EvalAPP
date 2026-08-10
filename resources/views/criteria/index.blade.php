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

<div class="alert alert-info">
    <i class="bi bi-info-circle me-1"></i>
    Bobot kriteria diatur per jabatan melalui menu <strong>Setting Proporsi</strong>.
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th>Nama Kriteria</th>
                    <th>Jenis</th>
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
                    <tr><td colspan="4" class="text-center text-muted py-4">Belum ada kriteria.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
