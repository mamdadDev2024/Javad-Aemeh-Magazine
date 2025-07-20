<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title')</title>
    @vite([
        'resources/css/app.css'
    ])
    <link rel="icon" type="image/x-icon" href="{{asset("assets/favicon.ico")}}">
    @yield('styles')
    <style>
        @font-face { font-family: "vasir"; src: url({{asset("assets/fonts/vasir.woff")}}); }
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
    {!! ToastMagic::styles() !!}
</head>

<body class="transition-colors font-vasir flex flex-col bg-gradient-to-b dark:from-slate-700 from-slate-200 dark:to-slate-800 to-slate-300">
    <x-admin_header/>
    @yield('body')
    <x-footer />

    <script>
        submitBtn = document.querySelector("#submitBtn").addEventListener("click" , () => {
            document.querySelector("#panelForm").submit();
        });
    </script>
    @vite([
        'resources/js/app.js'
    ])
    <script>
        window.addEventListener('scroll', function () {
            const header = document.getElementById('header');
            if (window.scrollY > 100) {
                header.classList.add('sticky-header');
            } else {
                header.classList.remove('sticky-header');
            }
        });
    </script>

    @yield('scripts')

    <div id="overlay" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50">
        <div class="loader"></div>
    </div>
    <script>
        document.addEventListener('submit', function(e) {
            document.getElementById('overlay').classList.remove('hidden');
        });
    </script>
    {!! ToastMagic::scripts() !!}
</body>

</html>

