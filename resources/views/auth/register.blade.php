<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun | DeisaHealth</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --primary: #059669;
            --primary-dark: #047857;
            --bg-auth: #f0fdf4;
            --text-main: #064e3b;
            --text-muted: #374151;
            --border: #d1fae5;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        
        body {
            font-family: 'Outfit', sans-serif;
            background: var(--bg-auth);
            color: var(--text-main);
        }

        .auth-container { display: flex; min-height: 100vh; }

        /* ─── Left Panel ─── */
        .auth-banner {
            flex: 1.2;
            background: #064e3b;
            background-image: 
                radial-gradient(at top left, rgba(52, 211, 153, 0.4), transparent 50%),
                url('https://www.transparenttextures.com/patterns/cubes.png');
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 80px;
            color: white;
            position: relative;
        }

        .logo-wrapper { display: flex; align-items: center; gap: 16px; margin-bottom: 48px; }
        .logo-icon {
            width: 56px; height: 56px;
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            font-size: 26px;
            color: #34d399;
            border: 1px solid rgba(255,255,255,0.1);
        }

        .banner-content h1 { font-size: 48px; font-weight: 800; line-height: 1.1; margin-bottom: 24px; letter-spacing: -0.03em; }
        
        .steps { margin-top: 48px; display: flex; flex-direction: column; gap: 32px; }
        .step-item { display: flex; align-items: flex-start; gap: 16px; }
        .step-num {
            width: 32px; height: 32px; border-radius: 50%; background: rgba(255,255,255,0.1);
            display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 14px; color: #34d399;
            flex-shrink: 0; border: 1px solid rgba(255,255,255,0.2);
        }
        .step-text h4 { font-size: 16px; font-weight: 700; margin-bottom: 4px; }
        .step-text p { font-size: 14px; opacity: 0.7; line-height: 1.5; }

        /* ─── Right Panel ─── */
        .auth-form-side {
            width: 580px;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px;
            box-shadow: -20px 0 60px rgba(0,0,0,0.05);
            z-index: 20;
            overflow-y: auto;
        }

        .form-card { width: 100%; max-width: 420px; }
        .form-card h2 { font-size: 32px; font-weight: 800; margin-bottom: 8px; letter-spacing: -0.02em; }
        .form-card p.subtitle { color: var(--text-muted); margin-bottom: 32px; font-size: 14px; line-height: 1.6; }

        .form-group { margin-bottom: 20px; }
        .form-label { display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px; color: var(--text-main); }
        
        .input-box { position: relative; }
        .input-box i { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 15px; }
        
        .form-input {
            width: 100%;
            padding: 12px 16px 12px 48px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            font-family: inherit;
            font-size: 15px;
            transition: all 0.2s;
            outline: none;
        }
        .form-input:focus { border-color: var(--primary); background: white; box-shadow: 0 0 0 4px rgba(5, 150, 105, 0.1); }

        .btn-submit {
            width: 100%;
            padding: 16px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 10px 25px rgba(5, 150, 105, 0.2);
            margin-top: 16px;
        }
        .btn-submit:hover { background: var(--primary-dark); transform: translateY(-2px); }

        .form-footer { margin-top: 24px; text-align: center; font-size: 14px; color: var(--text-muted); }
        .form-footer a { color: var(--primary); font-weight: 700; text-decoration: none; }

        .alert-box {
            background: #fffbeb; border-radius: 12px; padding: 16px; border-left: 4px solid #f59e0b;
            color: #92400e; font-size: 13px; margin-bottom: 24px; line-height: 1.5;
        }

        .error-badge {
            background: #fef2f2; color: #dc2626; padding: 12px; border-radius: 8px;
            font-size: 13px; font-weight: 600; margin-bottom: 20px; border: 1px solid #fee2e2;
        }

        @media (max-width: 1024px) {
            .auth-banner { display: none; }
            .auth-form-side { width: 100%; padding: 32px; }
        }
    </style>
</head>
<body>

<div class="auth-container">
    <div class="auth-banner">
        <div class="logo-wrapper">
            <div class="logo-icon"><i class="fas fa-heart-pulse"></i></div>
            <div style="font-size: 24px; font-weight: 800;">DeisaHealth</div>
        </div>
        <h1>Bergabung dalam<br>Layanan Kesehatan.</h1>
        
        <div class="steps">
            <div class="step-item">
                <div class="step-num">1</div>
                <div class="step-text">
                    <h4>Pendaftaran Akun</h4>
                    <p>Lengkapi data diri dan buat kata sandi aman untuk mengakses sistem.</p>
                </div>
            </div>
            <div class="step-item">
                <div class="step-num">2</div>
                <div class="step-text">
                    <h4>Verifikasi Keamanan</h4>
                    <p>Admin akan meninjau data Anda untuk memastikan keaslian akun.</p>
                </div>
            </div>
            <div class="step-item">
                <div class="step-num">3</div>
                <div class="step-text">
                    <h4>Mulai Beroperasi</h4>
                    <p>Setelah disetujui, Anda dapat mulai mengelola data kesehatan santri.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="auth-form-side">
        <div class="form-card">
            <h2>Daftar Akun</h2>
            <p class="subtitle">Silakan isi formulir di bawah untuk mendaftarkan akun petugas kesehatan baru.</p>

            @if ($errors->any())
                <div class="error-badge">
                    <i class="fas fa-triangle-exclamation"></i> Terjadi kesalahan pada input data Anda.
                </div>
            @endif

            <form method="POST" action="{{ route('register.submit') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Nama Lengkap</label>
                    <div class="input-box">
                        <i class="fas fa-user"></i>
                        <input type="text" name="name" class="form-input" placeholder="Nama sesuai identitas" required value="{{ old('name') }}">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Alamat Email</label>
                    <div class="input-box">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" class="form-input" placeholder="nama@email.com" required value="{{ old('email') }}">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Kata Sandi</label>
                    <div class="input-box">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" class="form-input" placeholder="Minimal 8 karakter" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Konfirmasi Sandi</label>
                    <div class="input-box">
                        <i class="fas fa-shield-check"></i>
                        <input type="password" name="password_confirmation" class="form-input" placeholder="Ulangi sandi" required>
                    </div>
                </div>

                <div class="alert-box">
                    <i class="fas fa-shield-halved"></i>
                    Pendaftaran Anda akan masuk ke daftar tunggu. <strong>Super Admin</strong> akan melakukan approval sebelum Anda dapat login.
                </div>

                <button type="submit" class="btn-submit">Daftar Sekarang</button>
            </form>

            <div class="form-footer">
                Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>
