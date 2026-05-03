<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - DEI Health</title>
    <link rel="shortcut icon" href="{{ asset('assets/images/logo/dei.png') }}" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    
    {{-- Vite Managed Assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/pages/auth.css') }}">
    @stack('styles')
</head>

<body>
    <div id="auth">
        @yield('content')
    </div>
    @stack('scripts')
</body>

</html>
