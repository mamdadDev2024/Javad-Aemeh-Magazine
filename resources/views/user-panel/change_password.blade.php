@extends("layouts.auth")

@section("title", "تغییر رمز عبور")

@section('body')
    <div class="w-full fixed top-0 p-5 bg-green-600">
        <a href="{{ route('home') }}" class="text-lg px-4 py-2 rounded-xl bg-blue-500 text-white">بازگشت به خانه</a>
    </div>

    <!-- Responsive form container -->
    <div class="flex items-center justify-center min-h-screen px-4 sm:px-6 lg:px-8">
        <form action="{{ route('user.do.change.password') }}" method="POST" class="bg-slate-800 rounded-2xl shadow-xl w-full max-w-md p-6 space-y-6">
            @csrf
            <input value="{{$user_id}}" name="id" type="hidden">
            <!-- password Field -->
            <div>
                <label for="password" class="block text-sm font-medium text-white">رمز عبور</label>
                <div class="mt-1">
                    <input name="password" id="password" type="password" class="transition-all focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 px-3 py-2 text-white bg-slate-700 rounded-lg w-full placeholder-gray-300" placeholder="رمز عبور خود را وارد کنید" required>
                </div>
                @error('password')
                    <div class="text-red-500 text-sm mt-2">{{ $message }}</div>
                @enderror
            </div>
            <!-- password confirm Field -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-white">تکرار رمز عبور</label>
                <div class="mt-1">
                    <input name="password_confirmation" id="password_confirmation" type="password" class="transition-all focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 px-3 py-2 text-white bg-slate-700 rounded-lg w-full placeholder-gray-300" placeholder="تکرار رمز عبور خود را وارد کنید" required>
                </div>
                @error('password_confirmation')
                    <div class="text-red-500 text-sm mt-2">{{ $message }}</div>
                @enderror
            </div>
            <x-captcha/>
            <!-- Submit Button -->
            <button type="submit" class="w-full py-3 text-white font-semibold bg-gradient-to-r from-blue-900 to-amber-600 hover:from-blue-800 hover:to-amber-500 transition-colors rounded-xl">ورود</button>
        </form>
    </div>
@endsection
