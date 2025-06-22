<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title')</title>
        <link rel="stylesheet" href="{{asset("build/assets/app-1.css")}}">
        <link rel="stylesheet" href="{{asset("build/assets/app-2.css")}}">
        <script src="{{asset("build/assets/app-3.js")}}"></script>
        <style>
                    @font-face { font-family: "vasir"; src: url({{asset("assets/fonts/vasir.woff")}}); }
        </style>
    </head>
    <body class="antialiased font-vasir">
        <div class="relative flex items-top justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center sm:pt-0">
            <div class=" flex absolute top-0 w-full justify-between gap-4 px-4 py-4">
                <a href="{{ route("home") }}" class=" underline text-lg font-bold hover:text-blue-400 text-blue-500 transition-all">
                    بازگشت به خانه
                </a>
                <a href="{{ url()->previous() }}" class="underline text-lg font-bold hover:text-blue-400 text-blue-500 transition-all">
                    بازگشت به قبل
                </a>
            </div>
            <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
                <div class="flex items-center pt-8 sm:justify-start sm:pt-0">
                    <div class="px-4 text-lg text-gray-500 border-r border-gray-400 tracking-wider">
                        @yield('code')
                    </div>

                    <div class="ml-4 text-lg text-gray-500 uppercase tracking-wider">
                        @yield('message')
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
