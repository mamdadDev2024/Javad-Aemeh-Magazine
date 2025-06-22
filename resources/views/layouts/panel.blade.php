@php
    $setting = Illuminate\Support\Facades\Storage::get("settings.json");
    $activate = json_decode($setting, true);
    $currentRouteName = Illuminate\Support\Facades\Route::currentRouteName();
    $allowedRoutes = ['login', 'register', 'user.profile'];

    if ($activate["activate"] || in_array($currentRouteName, $allowedRoutes)) {
        $siteLoad = true;
    } else {
        $siteLoad = false;
    }
@endphp

@if ($siteLoad)

<!DOCTYPE html>
<html lang="en" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{asset("build/assets/app-1.css")}}">
    <link rel="stylesheet" href="{{asset("build/assets/app-2.css")}}">
    <link rel="icon" type="image/x-icon" href="{{asset("assets/favicon.ico")}}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" integrity="sha512-1cK78a1o+ht2JcaW6g8OXYwqpev9+6GqOkz9xmBN9iUUhIndKtxwILGWYOSibOKjLsEdjyjZvYDq/cZwNeak0w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
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
</head>

<body class="transition-colors font-vasir flex flex-col bg-gradient-to-b dark:from-slate-700 from-slate-200 dark:to-slate-800 to-slate-300">
    <x-admin_header/>
    @yield('body')
    <x-footer />
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script>
        AOS.init()
        submitBtn = document.querySelector("#submitBtn").addEventListener("click" , () => {
            document.querySelector("#panelForm").submit();
        });
    </script>
    <script src="{{asset("build/assets/app-1.js")}}"></script>
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

@else
<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سایت در دسترس نیست</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }
        .error-container {
            text-align: center;
            padding: 2rem;
            border: 1px solid #ddd;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .error-title {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: #dc3545;
        }
        .error-text {
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
        }
    </style>
</head>

<body>
    <div class="error-container">
        <h1 class="error-title">سایت در دسترس نیست</h1>
        <p class="error-text">به دلیل مشکلات مالی و عدم پرداخت دستمزد برنامه‌نویس، سایت غیرفعال شده است.</p>
        <p>لطفا برای رفع مشکل با مدیریت سایت تماس بگیرید.</p>
    </div>
</body>

</html>
@endif
