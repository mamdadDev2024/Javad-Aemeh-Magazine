@extends("layouts.auth")

@section("title", "ورود به حساب کاربری")

@section('body')
<div class="w-full fixed top-0 p-5 bg-blue-500 dark:bg-teal-700">
    <a href="{{ route('home') }}" class="text-lg px-4 py-2 rounded-xl bg-white text-black">بازگشت به خانه</a>
</div>
    <div class="flex items-center justify-center min-h-screen px-4 sm:px-6 lg:px-8">
        <form action="{{ route('do_login') }}" method="POST" class="bg-slate-800 rounded-2xl shadow-xl w-full max-w-md p-6 space-y-6">
            @csrf
            <div>
                <label for="username" class="block text-sm font-medium text-white">نام کاربری</label>
                <div class="mt-1">
                    <input name="username" id="username" type="text" class="transition-all focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 px-3 py-2 text-white bg-slate-700 rounded-lg w-full placeholder-gray-300" placeholder="نام کاربری خود را وارد کنید" required>
                </div>
                @error('username')
                    <div class="text-red-500 text-sm mt-2">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-white">رمز عبور</label>
                <div class="mt-1">
                    <input name="password" id="password" type="password" class="transition-all focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 px-3 py-2 text-white bg-slate-700 rounded-lg w-full placeholder-gray-300" placeholder="رمز عبور خود را وارد کنید" required>
                </div>
                @error('password')
                    <div class="text-red-500 text-sm mt-2">{{ $message }}</div>
                @enderror
            </div>
            <div class="flex items-center justify-between">
                <a href="{{ route('register') }}" class="text-sm font-medium text-blue-400 hover:text-blue-300">ثبت نام</a>
                <a href="{{ route("forget") }}" class="text-sm font-medium text-blue-400 hover:text-blue-300">فراموشی رمز عبور</a>
            </div>
            <x-captcha/>
            <button type="submit" class="w-full py-3 text-white font-semibold bg-gradient-to-r from-blue-900 to-amber-600 hover:from-blue-800 hover:to-amber-500 transition-colors rounded-xl">ورود</button>
        </form>
    </div>
@endsection
