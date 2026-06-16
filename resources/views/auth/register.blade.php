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
            --primary: #0278D4;
            --primary-dark: #075EA6;
            --primary-navy: #12306F;
            --bg-auth: #F3F7FB;
            --text-main: #0F172A;
            --text-muted: #475569;
            --border: #CBD5E1;
            --card-bg: #FFFFFF;
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
            background-color: #12306F;
            background-image:
                linear-gradient(135deg, rgba(18, 48, 111, 0.98), rgba(2, 120, 212, 0.94)),
                radial-gradient(at top left, rgba(255, 255, 255, 0.18), transparent 42%),
                radial-gradient(at bottom right, rgba(255, 140, 0, 0.22), transparent 46%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 80px;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .auth-banner::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url('https://www.transparenttextures.com/patterns/cubes.png');
            opacity: 0.08;
            pointer-events: none;
        }

        .auth-banner > * { position: relative; z-index: 1; }

        .logo-wrapper { display: flex; align-items: center; gap: 16px; margin-bottom: 48px; }
        .logo-icon {
            width: 56px; height: 56px;
            background: rgba(255, 255, 255, 0.20);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            font-size: 26px;
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.24);
        }

        .banner-content h1,
        .auth-banner h1 { font-size: 48px; font-weight: 800; line-height: 1.1; margin-bottom: 24px; letter-spacing: -0.03em; color: #fff; text-shadow: 0 3px 16px rgba(0,0,0,0.32); }
        
        .steps { margin-top: 48px; display: flex; flex-direction: column; gap: 32px; }
        .step-item { display: flex; align-items: flex-start; gap: 16px; }
        .step-num {
            width: 32px; height: 32px; border-radius: 50%; background: rgba(255, 255, 255, 0.22);
            display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 14px; color: #ffffff;
            flex-shrink: 0; border: 1px solid rgba(255, 255, 255, 0.24);
        }
        .step-text h4 { font-size: 16px; font-weight: 700; margin-bottom: 4px; color: #fff; }
        .step-text p { font-size: 14px; line-height: 1.5; color: #F8FAFC; font-weight: 500; text-shadow: 0 2px 10px rgba(0,0,0,0.24); }

        /* ─── Right Panel ─── */
        .auth-form-side {
            width: 580px;
            background: var(--card-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px;
            box-shadow: -20px 0 60px rgba(15, 23, 42, 0.10);
            z-index: 20;
            overflow-y: auto;
            border-left: 1px solid var(--border);
        }

        .form-card { width: 100%; max-width: 420px; }
        .form-card h2 { font-size: 32px; font-weight: 800; margin-bottom: 8px; letter-spacing: -0.02em; color: var(--text-main); }
        .form-card p.subtitle { color: var(--text-muted); margin-bottom: 32px; font-size: 14px; line-height: 1.6; font-weight: 500; }

        .form-group { margin-bottom: 20px; }
        .form-label { display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px; color: var(--text-main); }
        
        .input-box { position: relative; }
        .input-box i { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 15px; }
        
        .form-input {
            width: 100%;
            padding: 12px 16px 12px 48px;
            border-radius: 12px;
            border: 1px solid #B6C4D5;
            background: #FFFFFF;
            font-family: inherit;
            font-size: 15px;
            color: var(--text-main);
            transition: all 0.2s;
            outline: none;
        }
        .form-input:focus { 
            border-color: var(--primary); 
            background: #FFFFFF; 
            box-shadow: 0 0 0 4px rgba(0, 144, 231, 0.12); 
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
            box-shadow: 0 10px 25px rgba(0, 144, 231, 0.22);
            margin-top: 16px;
        }
        .btn-submit:hover { background: var(--primary-dark); transform: translateY(-2px); box-shadow: 0 15px 30px rgba(0, 144, 231, 0.30); }

        .form-footer { margin-top: 24px; text-align: center; font-size: 14px; color: var(--text-muted); font-weight: 500; }
        .form-footer a { color: #075EA6; font-weight: 800; text-decoration: none; }

        .alert-box {
            background: #EAF6FF; border-radius: 12px; padding: 16px; border-left: 4px solid var(--primary);
            color: var(--text-main); font-size: 13px; margin-bottom: 24px; line-height: 1.5;
        }

        .error-badge {
            background: #FEF2F2; color: #B91C1C; padding: 12px; border-radius: 8px;
            font-size: 13px; font-weight: 600; margin-bottom: 20px; border: 1px solid #FECACA;
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
