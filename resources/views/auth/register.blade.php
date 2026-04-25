<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun | DEIHealth</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --primary: #00d25b;
            --primary-dark: #00ad4b;
            --bg-auth: #0b0c10;
            --text-main: #e4e4e4;
            --text-muted: #abb2b9;
            --border: #2c2e33;
            --card-bg: #111318;
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
            background: #000000;
            background-image: 
                radial-gradient(at top left, rgba(0, 210, 91, 0.2), transparent 50%),
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
            background: rgba(0, 210, 91, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            font-size: 26px;
            color: #00d25b;
            border: 1px solid rgba(0, 210, 91, 0.2);
        }

        .banner-content h1 { font-size: 48px; font-weight: 800; line-height: 1.1; margin-bottom: 24px; letter-spacing: -0.03em; color: #fff; }
        
        .steps { margin-top: 48px; display: flex; flex-direction: column; gap: 32px; }
        .step-item { display: flex; align-items: flex-start; gap: 16px; }
        .step-num {
            width: 32px; height: 32px; border-radius: 50%; background: rgba(0, 210, 91, 0.1);
            display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 14px; color: #00d25b;
            flex-shrink: 0; border: 1px solid rgba(0, 210, 91, 0.2);
        }
        .step-text h4 { font-size: 16px; font-weight: 700; margin-bottom: 4px; color: #fff; }
        .step-text p { font-size: 14px; opacity: 0.7; line-height: 1.5; color: rgba(255,255,255,0.7); }

        /* ─── Right Panel ─── */
        .auth-form-side {
            width: 580px;
            background: var(--card-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px;
            box-shadow: -20px 0 60px rgba(0,0,0,0.5);
            z-index: 20;
            overflow-y: auto;
            border-left: 1px solid var(--border);
        }

        .form-card { width: 100%; max-width: 420px; }
        .form-card h2 { font-size: 32px; font-weight: 800; margin-bottom: 8px; letter-spacing: -0.02em; color: #fff; }
        .form-card p.subtitle { color: var(--text-muted); margin-bottom: 32px; font-size: 14px; line-height: 1.6; }

        .form-group { margin-bottom: 20px; }
        .form-label { display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px; color: #fff; }
        
        .input-box { position: relative; }
        .input-box i { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 15px; }
        
        .form-input {
            width: 100%;
            padding: 12px 16px 12px 48px;
            border-radius: 12px;
            border: 1px solid var(--border);
            background: #191c24;
            font-family: inherit;
            font-size: 15px;
            color: #fff;
            transition: all 0.2s;
            outline: none;
        }
        .form-input:focus { 
            border-color: var(--primary); 
            background: #1d212b; 
            box-shadow: 0 0 0 4px rgba(0, 210, 91, 0.1); 
        }

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
            box-shadow: 0 10px 25px rgba(0, 210, 91, 0.2);
            margin-top: 16px;
        }
        .btn-submit:hover { background: var(--primary-dark); transform: translateY(-2px); box-shadow: 0 15px 30px rgba(0, 210, 91, 0.3); }

        .form-footer { margin-top: 24px; text-align: center; font-size: 14px; color: var(--text-muted); }
        .form-footer a { color: var(--primary); font-weight: 700; text-decoration: none; }

        .alert-box {
            background: rgba(0, 210, 91, 0.05); border-radius: 12px; padding: 16px; border-left: 4px solid var(--primary);
            color: #fff; font-size: 13px; margin-bottom: 24px; line-height: 1.5;
        }

        .error-badge {
            background: rgba(239, 68, 68, 0.1); color: #ef4444; padding: 12px; border-radius: 8px;
            font-size: 13px; font-weight: 600; margin-bottom: 20px; border: 1px solid rgba(239, 68, 68, 0.2);
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
            <div style="font-size: 24px; font-weight: 800;">DEIHealth</div>
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
