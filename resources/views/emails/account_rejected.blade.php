<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #ef4444;
            font-size: 24px;
            margin: 0;
        }
        .content {
            font-size: 16px;
            line-height: 1.5;
        }
        .reason-box {
            background-color: #fef2f2;
            border-left: 4px solid #ef4444;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .reason-title {
            font-weight: bold;
            color: #b91c1c;
            margin-bottom: 5px;
            font-size: 14px;
            text-transform: uppercase;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            font-size: 14px;
            color: #64748b;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Status Pendaftaran Akun DEIHealth</h1>
        </div>
        <div class="content">
            <p>Yth. <strong>{{ $user->name }}</strong>,</p>
            <p>Terima kasih telah melakukan pendaftaran akun pada sistem DEIHealth. Kami telah meninjau permohonan pendaftaran Anda.</p>
            <p>Mohon maaf, saat ini pendaftaran akun Anda <strong>belum dapat kami setujui (Ditolak)</strong>.</p>
            
            @if($reason)
                <div class="reason-box">
                    <div class="reason-title">Alasan Penolakan:</div>
                    <div>{{ $reason }}</div>
                </div>
            @endif

            <p>Jika Anda merasa ini adalah sebuah kesalahan atau ingin memperbaiki data pendaftaran, silakan hubungi administrator sistem atau balas email ini untuk informasi lebih lanjut.</p>
        </div>
        <div class="footer">
            <p>Pesan ini dikirim secara otomatis oleh Sistem Manajemen DEIHealth.</p>
            <p>&copy; {{ date('Y') }} DEIHealth. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
