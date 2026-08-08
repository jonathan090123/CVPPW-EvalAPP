<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — PWP Penilaian Karyawan</title>
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
        .login-card {
            width: 100%;
            max-width: 420px;
            border-radius: .75rem;
            box-shadow: 0 20px 50px rgba(0, 0, 0, .25);
        }
        .login-card .card-header {
            background-color: transparent;
            text-align: center;
            border-bottom: none;
            padding-top: 2rem;
            padding-bottom: .5rem;
        }
        .login-card .card-header i {
            font-size: 3rem;
            color: #1e3a5f;
        }
        .login-card .card-body {
            padding: 1rem 2rem 2rem;
        }
        .input-group-text {
            background-color: #fff;
            border-right: 0;
        }
        .input-group .form-control {
            border-left: 0;
        }
        .input-group:focus-within .input-group-text {
            border-color: #86b7fe;
        }
    </style>
</head>
<body>
<div class="card login-card">
    <div class="card-header">
        <i class="bi bi-bar-chart-steps"></i>
        <h4 class="fw-bold mt-2 mb-0">PWP Penilaian Karyawan</h4>
        <p class="text-muted small mb-0">Silakan login untuk melanjutkan</p>
    </div>
    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success py-2 small">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger py-2 small">
                {{ $errors->first() }}
            </div>
        @endif
        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">Username</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input type="text" name="username" value="{{ old('username') }}"
                           class="form-control @error('username') is-invalid @enderror"
                           placeholder="Masukkan username"
                           autocomplete="username" autofocus required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password"
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="Masukkan password"
                           autocomplete="current-password" required>
                </div>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" name="remember" class="form-check-input" id="remember">
                <label class="form-check-label small" for="remember">Ingat saya</label>
            </div>
            <button type="submit" class="btn btn-primary w-100 py-2">
                <i class="bi bi-box-arrow-in-right me-1"></i>Login
            </button>
        </form>
        <div class="text-center mt-3">
            <span class="text-muted small">Belum punya akun?</span>
            <a href="{{ route('register') }}" class="small fw-semibold">Daftar di sini</a>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>