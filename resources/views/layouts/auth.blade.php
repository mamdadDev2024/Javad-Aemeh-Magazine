<!DOCTYPE html>
<html lang="en" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title> @yield('title')</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link rel="stylesheet" href="{{asset("build/assets/app-1.css")}}">
    <link rel="stylesheet" href="{{asset("build/assets/app-2.css")}}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" integrity="sha512-1cK78a1o+ht2JcaW6g8OXYwqpev9+6GqOkz9xmBN9iUUhIndKtxwILGWYOSibOKjLsEdjyjZvYDq/cZwNeak0w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
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
</head>

<body
    class="transition-colors font-vasir flex flex-col bg-gradient-to-b min-h-svh dark:from-slate-700 from-slate-200 dark:to-slate-800 to-slate-300">
    @yield('body')
    @yield('scripts')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    @if (session('alert'))
        @php
            $icon = session('alert.icon', 'error');
            $text = session('alert.text', 'خطای نامشخص');
        @endphp
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Toastify({
                    text: `{{ $icon === 'success' ? '✅' : '❌' }}{{ $text }}`,
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
                        fontSize: "14px",
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
