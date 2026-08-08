@extends('layouts.app')
@section('title', 'Daftar Karyawan')

@section('content')
<div class="page-header">
    <div>
        <h4 class="page-title mb-1"><i class="bi bi-people me-2"></i>Daftar Karyawan</h4>
        <p class="page-subtitle">Pantau data karyawan dengan pencarian cepat dan tampilan yang lebih rapi di layar kecil maupun besar.</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        @if (auth()->user()->isOwner())
            <a href="{{ route('employees.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i>Tambah Karyawan
            </a>
        @else
            <span class="badge bg-info text-dark align-self-center" style="font-size:.7rem">
                <i class="bi bi-lock me-1"></i>Hanya Owner yang dapat mengubah data karyawan
            </span>
        @endif
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <h5 class="mb-0">Total <span class="badge bg-secondary ms-2" style="font-size:.75rem">{{ $employees->total() }} orang</span></h5>
        </div>

        <form action="{{ route('employees.index') }}" method="GET" id="filterForm" class="row g-2 align-items-center">
            <div class="col-12 col-md-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" value="{{ $search }}"
                           class="form-control" placeholder="Cari nama / NIP..."
                           oninput="clearTimeout(window.__fTimer);window.__fTimer=setTimeout(()=>this.form.submit(),400)">
                </div>
            </div>
            <div class="col-6 col-md-3">
                <select name="department" class="form-select form-select-sm" onchange="document.getElementById('filterForm').submit()">
                    <option value="">Semua Departemen</option>
                    @foreach ($departments as $d)
                        <option value="{{ $d }}" {{ $department === $d ? 'selected' : '' }}>{{ $d }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-3">
                <select name="position" class="form-select form-select-sm" onchange="document.getElementById('filterForm').submit()">
                    <option value="">Semua Jabatan</option>
                    @foreach ($positions as $p)
                        <option value="{{ $p }}" {{ $position === $p ? 'selected' : '' }}>{{ $p }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-2 d-flex gap-1">
                <a href="{{ route('employees.index') }}" class="btn btn-sm btn-outline-secondary flex-fill" title="Reset filter">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                </a>
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th>NIP</th>
                        <th>Nama</th>
                        <th>Departemen</th>
                        <th>Jabatan</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($employees as $i => $employee)
                        <tr>
                            <td>{{ $employees->firstItem() + $i }}</td>
                            <td>{{ $employee->nip }}</td>
                            <td>{{ $employee->name }}</td>
                            <td>{{ $employee->department ?? '-' }}</td>
                            <td>
                                @if ($employee->position)
                                    @positionBadge($employee->position)
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if (auth()->user()->isOwner())
                                    <div class="d-flex flex-wrap gap-1">
                                        <a href="{{ route('employees.edit', $employee) }}" class="btn btn-warning btn-sm">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Hapus karyawan ini?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-muted small"><i class="bi bi-lock"></i></span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">
                            Tidak ada karyawan yang cocok.
                            <a href="{{ route('employees.index') }}">Reset filter</a>.
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if ($employees->hasPages())
<div class="mt-3 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
    <small class="text-muted">
        Menampilkan {{ $employees->firstItem() }}–{{ $employees->lastItem() }} dari {{ $employees->total() }} karyawan
    </small>
    <nav>
        <ul class="pagination pagination-sm mb-0">
            @if ($employees->onFirstPage())
                <li class="page-item disabled"><span class="page-link">‹</span></li>
            @else
                <li class="page-item"><a class="page-link" href="{{ $employees->previousPageUrl() }}">‹</a></li>
            @endif

            @foreach ($employees->getUrlRange(1, $employees->lastPage()) as $page => $url)
                @if ($page == $employees->currentPage())
                    <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                @else
                    <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                @endif
            @endforeach

            @if ($employees->hasMorePages())
                <li class="page-item"><a class="page-link" href="{{ $employees->nextPageUrl() }}">›</a></li>
            @else
                <li class="page-item disabled"><span class="page-link">›</span></li>
            @endif
        </ul>
    </nav>
</div>
@endif
@endsection