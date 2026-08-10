@extends('layouts.app')
@section('title', 'Pengaturan Akun')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-gear me-2"></i>Pengaturan Akun</h4>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="mb-3">
            <label class="form-label fw-semibold">Nama (Username)</label>
            <input type="text" value="{{ auth()->user()->username }}" class="form-control" disabled>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Jabatan</label>
            <input type="text" value="{{ auth()->user()->position }}" class="form-control" disabled>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Departemen</label>
            <input type="text" value="{{ auth()->user()->department() ?? '-' }}" class="form-control" disabled>
        </div>

        <hr>

        <form action="{{ route('settings.update') }}" method="POST">
            @csrf @method('PUT')

            <div class="mb-3">
                <label class="form-label fw-semibold">Password Saat Ini</label>
                <input type="password" name="current_password"
                       class="form-control @error('current_password') is-invalid @enderror"
                       placeholder="Masukkan password saat ini" autocomplete="current-password" required>
                @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Password Baru</label>
                <input type="password" name="password"
                       class="form-control @error('password') is-invalid @enderror"
                       placeholder="Minimal 4 karakter" autocomplete="new-password" required>
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Konfirmasi Password Baru</label>
                <input type="password" name="password_confirmation"
                       class="form-control"
                       placeholder="Ulangi password baru" autocomplete="new-password" required>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Simpan Password
                </button>
                <a href="{{ route('assessments.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Kembali
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
