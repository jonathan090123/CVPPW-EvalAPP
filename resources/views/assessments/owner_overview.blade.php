@extends('layouts.app')
@section('title', 'Ringkasan Penilaian Owner')

@section('content')
<div class="page-header">
    <div>
        <h4 class="page-title mb-1"><i class="bi bi-clipboard2-data me-2"></i>Ringkasan Penilaian Owner</h4>
        <p class="page-subtitle">Lihat hasil akumulasi secara cepat dan gunakan filter untuk memfokuskan data yang diinginkan.</p>
    </div>
</div>

<form method="GET" action="{{ route('assessments.ownerOverview') }}" class="row g-3 mb-4" id="owner-filter-form">
    <div class="col-12 col-md-4">
        <label class="form-label fw-semibold small">Cari Nama / Departemen / Jabatan</label>
        <input type="text" name="search" class="form-control form-control-sm" value="{{ $search }}" placeholder="Ketik pencarian..." oninput="document.getElementById('owner-filter-form').submit()">
    </div>
    <div class="col-12 col-md-3">
        <label class="form-label fw-semibold small">Filter Jabatan</label>
        <select name="position" class="form-select form-select-sm" onchange="this.form.submit()">
            <option value="">Semua Jabatan</option>
            @foreach ($positions as $position)
                <option value="{{ $position }}" {{ $selectedPosition === $position ? 'selected' : '' }}>{{ $position }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-12 col-md-3">
        <label class="form-label fw-semibold small">Filter Departemen</label>
        <select name="department" class="form-select form-select-sm" onchange="this.form.submit()">
            <option value="">Semua Departemen</option>
            @foreach ($departments as $department)
                <option value="{{ $department }}" {{ $selectedDepartment === $department ? 'selected' : '' }}>{{ $department }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-12 col-md-2 d-flex align-items-end">
        @php
            $hasFilter = $selectedPosition !== '' || $selectedDepartment !== '' || $search !== '';
        @endphp
        <a href="{{ route('assessments.ownerOverview') }}" class="btn btn-sm w-100 {{ $hasFilter ? 'btn-warning text-dark' : 'btn-outline-secondary' }}">
            <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
        </a>
    </div>
</form>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white">Ringkasan Akumulasi Semua Karyawan</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Peringkat</th>
                        <th>Karyawan</th>
                        <th>Departemen</th>
                        <th>Jumlah Penilaian</th>
                        <th>Total Skor</th>
                        <th>Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($accumulated as $row)
                        @php
                            $positionRank = $loop->iteration;
                            $rowClass = $positionRank === 1 ? 'table-warning' : ($positionRank === 2 ? 'table-secondary' : ($positionRank === 3 ? 'table-light' : ''));
                            $rankBadge = $positionRank === 1 ? '<span class="badge bg-warning text-dark"><i class="bi bi-trophy-fill me-1"></i>Top 1</span>' : ($positionRank === 2 ? '<span class="badge bg-secondary"><i class="bi bi-award-fill me-1"></i>Top 2</span>' : ($positionRank === 3 ? '<span class="badge" style="background-color:#cd7f32;"><i class="bi bi-award-fill me-1"></i>Top 3</span>' : '<span class="badge bg-light text-muted">#'.$positionRank.'</span>'));
                        @endphp
                        <tr class="{{ $rowClass }}">
                            <td class="fw-semibold">{!! $rankBadge !!}</td>
                            <td class="fw-semibold">
                                <a href="#employee-details-{{ $row['employee']->id }}" class="text-decoration-none" data-bs-toggle="collapse" role="button" aria-expanded="false">
                                    {{ $row['employee']->name }}
                                </a>
                            </td>
                            <td>{{ $row['employee']->department }}</td>
                            <td>{{ $row['count'] }}</td>
                            <td>{{ number_format($row['total'], 4) }}</td>
                            <td>
                                <a href="#employee-details-{{ $row['employee']->id }}" class="btn btn-outline-secondary btn-sm" data-bs-toggle="collapse" role="button" aria-expanded="false">
                                    <i class="bi bi-list-ul me-1"></i>Detail
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6" class="p-0">
                                <div class="collapse px-3 py-2" id="employee-details-{{ $row['employee']->id }}">
                                    <div class="small fw-semibold mb-2">Riwayat penilaian untuk {{ $row['employee']->name }}</div>
                                    <ul class="list-group list-group-flush">
                                        @foreach ($row['details'] as $detail)
                                            <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                                <span>{{ $detail['assessment']->name ?? $detail['assessment']->period }} <small class="text-muted">({{ $detail['assessment']->period }})</small></span>
                                                <span class="text-muted">Rank {{ $detail['rank'] }} · {{ number_format($detail['score'], 4) }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">Belum ada data untuk filter ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
