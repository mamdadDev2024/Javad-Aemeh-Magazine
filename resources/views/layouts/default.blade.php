<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title')</title>
    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/favicon.ico') }}">
    @yield('styles')
    <style>
        @font-face { font-family: "vasir"; src: url({{ asset('assets/fonts/vasir.woff') }}); }
        @media not all and (min-width: 1024px) {
            .h-82 {
                height: 22rem;
            }
        }

        .sticky-header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 50;
        }

        /* استایل‌های اسپینر */
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
    </style>
</head>

<body class="transition-all duration-200 font-vasir flex flex-col bg-gradient-to-b dark:from-slate-700 from-slate-200 dark:to-slate-800 to-slate-300">
    <x-header/>
    @yield('body')
    <x-footer />
    <script>
        AOS.init();
    </script>
    <script>
        window.addEventListener('scroll', function () {
            const header = document.getElementById('header');
            if (window.scrollY > 200) {
                header.classList.add('sticky-header');
            } else {
                header.classList.remove('sticky-header');
            }
        });
    </script>

    @yield('scripts')
    @if (session('alert'))
        @php
            $icon = session('alert.icon', 'error');
            $text = session('alert.text', 'خطای نامشخص');
        @endphp
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Toastify({
                    text: `{{ $icon === 'success' ? '✅' : '' }}{{ $text }}`,
                    duration: 5000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    stopOnFocus: true,
                    style: {
                        background: "{{ $icon === 'success' ? 'linear-gradient(to right, #28a745, #5bc0de)' : 'linear-gradient(to right, #dc3545, #ffc107)' }}",
                        borderRadius: "8px",
                        boxShadow: "0 4px 6px rgba(0, 0, 0, 0.1)",
                        padding: "12px 20px",
                        color: "#fff",
                        fontSize: "14px"
                    },
                }).showToast();
            });
        </script>
    @endif
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
