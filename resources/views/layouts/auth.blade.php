<!DOCTYPE html>
<html lang="en" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title> @yield('title')</title>
    @vite([
        'resources/css/app.css',
    ])
    <link rel="icon" type="image/x-icon" href="{{asset("assets/favicon.ico")}}">
    @yield("styles")
    <style>
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
        @font-face { font-family: "vasir"; src: url({{asset("assets/fonts/vasir.woff")}}); }
        @media not all and (min-width: 1024px) {
            .h-82 {
                height: 22rem
                    /* 320px */
                ;
            }
        }
    </style>
    {!! ToastMagic::styles() !!}
</head>

<body
    class="transition-colors font-vasir flex flex-col bg-gradient-to-b min-h-svh dark:from-slate-700 from-slate-200 dark:to-slate-800 to-slate-300">
    @yield('body')
    @yield('scripts')

    @vite([
        'resources/js/app.js'
    ])

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
