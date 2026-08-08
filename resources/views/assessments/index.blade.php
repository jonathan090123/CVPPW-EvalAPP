@extends('layouts.app')
@section('title', 'Daftar Penilaian')

@section('content')
<div class="page-header">
    <div>
        <h4 class="page-title mb-1"><i class="bi bi-clipboard-data me-2"></i>Daftar Penilaian</h4>
        <p class="page-subtitle">Kelola penilaian, lihat hasil ranking, dan pantau riwayat penilaian dengan lebih nyaman.</p>
    </div>
    <a href="{{ route('assessments.create') }}" class="btn btn-primary btn-sm px-3">
        <i class="bi bi-plus-lg me-1"></i>Buat Penilaian Baru
    </a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th>Nama Penilaian</th>
                        <th>Periode</th>
                        <th>Deskripsi</th>
                        <th>Dibuat</th>
                        <th width="22%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($assessments as $i => $assessment)
                        <tr>
                            <td>{{ $assessments->firstItem() + $i }}</td>
                            <td><strong>{{ $assessment->name ?? $assessment->period }}</strong></td>
                            <td>{{ $assessment->period }}</td>
                            <td class="text-muted small">{{ $assessment->description ?? '-' }}</td>
                            <td class="small">{{ $assessment->created_at->format('d M Y') }}</td>
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    <a href="{{ route('assessments.show', $assessment) }}" class="btn btn-info btn-sm text-white">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('ranking.show', $assessment) }}" class="btn btn-success btn-sm">
                                        <i class="bi bi-trophy"></i> Ranking
                                    </a>
                                    <a href="{{ route('assessments.edit', $assessment) }}" class="btn btn-warning btn-sm">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('assessments.destroy', $assessment) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Hapus penilaian ini?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">Belum ada data penilaian.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="mt-3">{{ $assessments->links() }}</div>
@endsection
