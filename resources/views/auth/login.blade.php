<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Enterprise Sistem Karyawan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', system-ui, sans-serif;
            padding: 1.5rem;
        }
        .login-card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
            width: 100%;
            max-width: 440px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .login-header {
            background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%);
            padding: 2.5rem 2rem 2rem;
            color: #ffffff;
            text-align: center;
        }
        .login-header .brand-icon {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(8px);
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-bottom: 1rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .login-body {
            padding: 2rem 2.25rem;
        }
        .form-control {
            padding: 0.75rem 1rem;
            border-radius: 10px;
            border: 1.5px solid #e2e8f0;
            font-size: 0.925rem;
        }
        .form-control:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }
        .btn-login {
            background: #2563eb;
            border: none;
            padding: 0.8rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.2s;
        }
        .btn-login:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
        }
        .demo-box {
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 12px;
            padding: 1rem;
            margin-top: 1.5rem;
            font-size: 0.8rem;
        }
        .demo-btn {
            cursor: pointer;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-header">
        <div class="brand-icon">
            <i class="bi bi-building-fill-lock"></i>
        </div>
        <h4 class="fw-bold mb-1">Enterprise HR</h4>
        <p class="mb-0 text-white-50" style="font-size: 0.88rem;">Sistem Manajemen &amp; Presensi Karyawan</p>
    </div>

    <div class="login-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 small" role="alert">
                <i class="bi bi-check-circle-fill"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 small" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label small fw-semibold text-secondary">Alamat Email</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                    <input type="email" class="form-control border-start-0 @error('email') is-invalid @enderror"
                           id="email" name="email" value="{{ old('email') }}"
                           placeholder="nama@company.com" required autofocus>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label small fw-semibold text-secondary">Kata Sandi</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                    <input type="password" class="form-control border-start-0 @error('password') is-invalid @enderror"
                           id="password" name="password" placeholder="••••••••" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label small text-muted" for="remember">
                        Ingat Saya
                    </label>
                </div>
                <span class="text-muted small">Aturan Jam Masuk: 07:30</span>
            </div>

            <button type="submit" class="btn btn-primary btn-login w-100 text-white">
                <i class="bi bi-box-arrow-in-right me-2"></i>Masuk ke Sistem
            </button>
        </form>

        {{-- DEMO CREDENTIALS QUICK FILL --}}
        <div class="demo-box">
            <div class="fw-bold text-secondary mb-2 d-flex align-items-center gap-1">
                <i class="bi bi-lightning-charge-fill text-warning"></i> Akun Demo Cepat (Klik untuk isi):
            </div>
            <div class="d-flex flex-column gap-2">
                <button type="button" class="btn btn-sm btn-outline-success text-start d-flex justify-content-between align-items-center border-2"
                        onclick="fillCreds('budi@company.com', 'password')">
                    <span><strong>⭐ Budi Santoso:</strong> budi@company.com</span>
                    <span class="badge bg-success">Belum Absen (Siap Tes!)</span>
                </button>
                <button type="button" class="btn btn-sm btn-outline-primary text-start d-flex justify-content-between align-items-center"
                        onclick="fillCreds('admin@company.com', 'password')">
                    <span><strong>Admin HR:</strong> admin@company.com</span>
                    <span class="badge bg-primary">Full Access</span>
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary text-start d-flex justify-content-between align-items-center"
                        onclick="fillCreds('ahmad@company.com', 'password')">
                    <span><strong>Ahmad Fauzi:</strong> ahmad@company.com</span>
                    <span class="badge bg-secondary">Karyawan Tetap</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function fillCreds(email, password) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = password;
    }
</script>
</body>
</html>