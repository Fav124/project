<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 – Akses Ditolak | DEIHealth</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #1e3a8a, #1e40af, #2563eb);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            color: white;
        }
        .container {
            text-align: center;
            max-width: 480px;
        }
        .icon {
            font-size: 72px;
            margin-bottom: 24px;
            opacity: .9;
        }
        h1 { font-size: 80px; font-weight: 800; line-height: 1; margin-bottom: 8px; opacity: .95; }
        h2 { font-size: 24px; font-weight: 700; margin-bottom: 12px; }
        p  { font-size: 15px; opacity: .8; line-height: 1.7; margin-bottom: 32px; }
        .btn-home {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: rgba(255,255,255,.15);
            color: white;
            border: 1.5px solid rgba(255,255,255,.3);
            border-radius: 10px;
            text-decoration: none;
            font-size: 15px;
            font-weight: 600;
            transition: background .2s;
            backdrop-filter: blur(10px);
        }
        .btn-home:hover { background: rgba(255,255,255,.25); }
    </style>
</head>
<body>
<div class="container">
    <div class="icon"><i class="fas fa-shield-halved"></i></div>
    <h1>403</h1>
    <h2>Akses Ditolak</h2>
    <p>Anda tidak memiliki izin untuk mengakses halaman ini. Jika Anda merasa ini adalah kesalahan, hubungi Super Admin.</p>
    <a href="{{ url()->previous() !== url()->current() ? url()->previous() : '/' }}" class="btn-home">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>
</body>
</html>
