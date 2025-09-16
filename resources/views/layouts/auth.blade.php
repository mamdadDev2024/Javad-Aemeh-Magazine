<!DOCTYPE html>
<html lang="en" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite('resources/css/app.css')
    @else
        <link rel="stylesheet" href="{{ asset('assets/app1.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/app2.css') }}">
    @endif

    <link rel="icon" type="image/x-icon" href="{{ asset('assets/favicon.ico') }}">
    @yield('styles')

    <style>
        @font-face {
            font-family: "vasir";
            src: url({{ asset("assets/fonts/vasir.woff")}}) format("woff");
            font-weight: normal;
            font-style: normal;
        }
        .loader {
            border: 8px solid #f3f3f3;
            border-top: 8px solid #3490dc;
            border-radius: 50%;
            width: 80px;
            height: 80px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        @media not all and (min-width: 1024px) {
            .h-82 { height: 22rem; }
        }
    </style>

    {!! ToastMagic::styles() !!}
</head>

<body class="transition-colors font-vasir flex flex-col bg-gradient-to-b min-h-screen dark:from-slate-700 from-slate-200 dark:to-slate-800 to-slate-300">
    @yield('body')
    @yield('scripts')

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite('resources/js/app.js')
    @else
        <script src="{{ asset('assets/app.js') }}"></script>
    @endif

    {!! ToastMagic::scripts() !!}

    <div id="overlay" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50">
        <div class="loader"></div>
    </div>

    <script>
        document.addEventListener('submit', function(e) {
            document.getElementById('overlay').classList.remove('hidden');
        });
    </script>
</body>
</html>
