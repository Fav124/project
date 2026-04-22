<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | DeisaHealth</title>
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
            --card-bg: #191c24;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        
        body {
            font-family: 'Outfit', sans-serif;
            background: var(--bg-auth);
            color: var(--text-main);
            overflow: hidden;
        }

        .auth-container {
            display: flex;
            min-height: 100vh;
        }

        /* ─── Left Panel ─── */
        .auth-banner {
            flex: 1.2;
            background: #000000;
            background-image: 
                radial-gradient(at top left, rgba(0, 210, 91, 0.2), transparent 50%),
                radial-gradient(at bottom right, rgba(0, 210, 91, 0.1), transparent 50%),
                url('https://www.transparenttextures.com/patterns/cubes.png');
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 80px;
            color: white;
            position: relative;
        }

        .banner-content { position: relative; z-index: 10; max-width: 500px; }
        
        .logo-wrapper {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 48px;
        }
        
        .logo-icon {
            width: 56px; height: 56px;
            background: rgba(0, 210, 91, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            font-size: 26px;
            color: #00d25b;
            border: 1px solid rgba(0, 210, 91, 0.2);
            box-shadow: 0 0 30px rgba(0, 210, 91, 0.2);
        }

        .brand-name { font-size: 32px; font-weight: 800; letter-spacing: -0.02em; color: #fff; }
        .brand-name span { color: #00d25b; }

        .banner-content h1 { font-size: 48px; font-weight: 800; line-height: 1.1; margin-bottom: 24px; letter-spacing: -0.03em; color: #fff; }
        .banner-content p { font-size: 18px; color: rgba(255,255,255,0.7); line-height: 1.6; font-weight: 400; }

        .feature-list { margin-top: 56px; display: flex; flex-direction: column; gap: 20px; }
        .feature-tag {
            display: flex; align-items: center; gap: 12px;
            font-size: 15px; font-weight: 500; color: rgba(255,255,255,0.9);
        }
        .feature-tag i { color: #00d25b; font-size: 18px; }

        /* ─── Right Panel ─── */
        .auth-form-side {
            width: 550px;
            background: #111318;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px;
            box-shadow: -20px 0 60px rgba(0,0,0,0.5);
            z-index: 20;
            border-left: 1px solid var(--border);
        }

        .form-card { width: 100%; max-width: 400px; }
        .form-card h2 { font-size: 32px; font-weight: 800; margin-bottom: 8px; letter-spacing: -0.02em; color: #fff; }
        .form-card p.subtitle { color: var(--text-muted); margin-bottom: 40px; font-size: 15px; }

        .form-group { margin-bottom: 24px; }
        .form-label { display: block; font-size: 14px; font-weight: 700; margin-bottom: 8px; color: #fff; }
        
        .input-box { position: relative; }
        .input-box i { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 16px; }
        
        .form-input {
            width: 100%;
            padding: 14px 16px 14px 48px;
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

        .password-toggle {
            position: absolute; right: 16px; top: 50%; transform: translateY(-50%);
            background: none; border: none; cursor: pointer; color: var(--text-muted);
        }

        .btn-submit {
            width: 100%;
            padding: 16px;
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 10px 25px rgba(0, 210, 91, 0.2);
            margin-top: 8px;
        }
        .btn-submit:hover { 
            background: var(--primary-dark); 
            transform: translateY(-2px); 
            box-shadow: 0 15px 30px rgba(0, 210, 91, 0.3); 
        }

        .form-footer { margin-top: 32px; text-align: center; font-size: 14px; color: var(--text-muted); }
        .form-footer a { color: var(--primary); font-weight: 700; text-decoration: none; }

        .wa-footer {
            margin-top: 48px;
            padding-top: 32px;
            border-top: 1px solid var(--border);
            text-align: center;
        }
        .wa-btn {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 12px 24px;
            background: #25d366;
            color: white;
            border-radius: 99px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 700;
            transition: all 0.2s;
            box-shadow: 0 4px 15px rgba(37, 211, 102, 0.3);
        }
        .wa-btn:hover { background: #1ebe5a; transform: scale(1.05); }

        .badge-error {
            background: rgba(239, 68, 68, 0.1); color: #ef4444; padding: 12px; border-radius: 8px;
            font-size: 13px; font-weight: 600; margin-bottom: 24px; display: flex; align-items: center; gap: 10px;
            border: 1px solid rgba(239, 68, 68, 0.2);
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
        <div class="banner-content">
            <div class="logo-wrapper">
                <div class="logo-icon"><i class="fas fa-heart-pulse"></i></div>
                <div class="brand-name">DeisaHealth</div>
            </div>
            <h1>Kelola Kesehatan<br>Lebih Modern.</h1>
            <p>Platform terintegrasi untuk pemantauan kesehatan santri dan manajemen operasional UKS secara efisien.</p>
            
            <div class="feature-list">
                <div class="feature-tag"><i class="fas fa-check-circle"></i> Rekam Medis Digital & Terenkripsi</div>
                <div class="feature-tag"><i class="fas fa-check-circle"></i> Monitoring Inventori Obat Real-time</div>
                <div class="feature-tag"><i class="fas fa-check-circle"></i> Laporan Analitik Kesehatan Mingguan</div>
            </div>
        </div>
    </div>

    <div class="auth-form-side">
        <div class="form-card">
            <h2>Masuk</h2>
            <p class="subtitle">Gunakan kredensial akun Anda untuk mengakses dashboard.</p>

            @if(session('error'))
                <div class="badge-error"><i class="fas fa-circle-exclamation"></i> {{ session('error') }}</div>
            @endif
            @if(session('success'))
                <div class="badge-error" style="background:#dcfce7; color:#15803d;"><i class="fas fa-circle-check"></i> {{ session('success') }}</div>
            @endif

            <form method="POST" action="{{ route('login.submit') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Email Kantor / Petugas</label>
                    <div class="input-box">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" class="form-input" placeholder="nama@deisahealth.com" required value="{{ old('email') }}" autofocus>
                    </div>
                    @error('email') <div style="color:#ef4444; font-size:12px; margin-top:4px; font-weight:600;">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Kata Sandi</label>
                    <div class="input-box">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" id="password" class="form-input" placeholder="••••••••" required>
                        <button type="button" class="password-toggle" onclick="togglePass()">
                            <i class="fas fa-eye" id="eye-icon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-submit">Masuk Sekarang</button>
            </form>

            <div class="form-footer">
                Belum punya akses? <a href="{{ route('register') }}">Daftar Akun Petugas</a>
            </div>

            <div class="wa-footer">
                <p style="font-size:13px; color:var(--text-muted); margin-bottom:12px;">Kesulitan masuk? Hubungi Administrator:</p>
                <a href="https://wa.me/{{ config('app.admin_whatsapp', '6281234567890') }}?text={{ urlencode('Halo Admin, saya butuh bantuan untuk login ke DeisaHealth.') }}" target="_blank" class="wa-btn">
                    <i class="fab fa-whatsapp"></i> Chat Support
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePass() {
        const p = document.getElementById('password');
        const i = document.getElementById('eye-icon');
        if(p.type === 'password') {
            p.type = 'text';
            i.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            p.type = 'password';
            i.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }
</script>
</body>
</html>
