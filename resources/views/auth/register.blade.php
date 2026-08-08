<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun — PWP Penilaian Karyawan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1e3a5f 0%, #2c5376 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .register-card {
            width: 100%;
            max-width: 460px;
            border-radius: .75rem;
            box-shadow: 0 20px 50px rgba(0, 0, 0, .25);
        }
        .register-card .card-header {
            background-color: transparent;
            text-align: center;
            border-bottom: none;
            padding-top: 2rem;
            padding-bottom: .5rem;
        }
        .register-card .card-header i {
            font-size: 2.5rem;
            color: #1e3a5f;
        }
        .register-card .card-body {
            padding: 1rem 2rem 2rem;
        }
    </style>
</head>
<body>
<div class="card register-card">
    <div class="card-header">
        <i class="bi bi-person-plus"></i>
        <h4 class="fw-bold mt-2 mb-0">Daftar Akun</h4>
        <p class="text-muted small mb-0">Buat akun baru untuk penilaian karyawan</p>
    </div>
    <div class="card-body">
        @if ($errors->any())
            <div class="alert alert-danger py-2 small">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('register') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">Username</label>
                <input type="text" name="username" value="{{ old('username') }}"
                       class="form-control @error('username') is-invalid @enderror"
                       placeholder="Pilih username"
                       autocomplete="username" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Jabatan</label>
                <select name="position" class="form-select @error('position') is-invalid @enderror" required>
                    <option value="" disabled {{ old('position') ? '' : 'selected' }}>-- Pilih Jabatan --</option>
                    @foreach ($positions as $value => $label)
                        <option value="{{ $value }}" {{ old('position') === $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                <small class="text-muted">Login yang lebih tinggi (Kepala Bagian/Koordinator) dapat mengakses & menilai karyawan di bawahnya.</small>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Password</label>
                <input type="password" name="password"
                       class="form-control @error('password') is-invalid @enderror"
                       placeholder="Minimal 4 karakter"
                       autocomplete="new-password" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Konfirmasi Password</label>
                <input type="password" name="password_confirmation"
                       class="form-control"
                       placeholder="Ulangi password"
                       autocomplete="new-password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 py-2">
                <i class="bi bi-person-check me-1"></i>Daftar
            </button>
        </form>
        <div class="text-center mt-3">
            <span class="text-muted small">Sudah punya akun?</span>
            <a href="{{ route('login') }}" class="small fw-semibold">Login di sini</a>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>