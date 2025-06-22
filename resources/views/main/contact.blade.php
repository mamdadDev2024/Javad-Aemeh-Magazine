@extends("layouts.auth")

@section("title", "تماس با ادمین")

@section('body')
    <div class="w-full fixed top-0 p-5 bg-green-600">
        <a href="{{ route('home') }}" class="text-lg px-4 py-2 rounded-xl bg-blue-500 text-white">بازگشت به خانه</a>
    </div>
    <div class="flex items-center justify-center min-h-screen px-4 sm:px-6 lg:px-8">
        <form action="{{ route('do.contact') }}" method="POST" class="bg-slate-800 rounded-2xl shadow-xl w-full max-w-md p-6 space-y-6">
            @csrf
            <div>
                <label for="number" class="block text-sm font-medium text-white">شماره تماس</label>
                <div class="mt-1">
                    <input name="number" id="number" class="transition-all max-h-96 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 px-3 py-2 text-white bg-slate-700 rounded-lg w-full placeholder-gray-300" placeholder="شماره خود را بنویسید ." required>
                </div>
                @error('number')
                    <div class="text-red-500 text-sm mt-2">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <label for="body" class="block text-sm font-medium text-white">متن</label>
                <div class="mt-1">
                    <textarea name="body" id="body" class="transition-all max-h-96 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 px-3 py-2 text-white bg-slate-700 rounded-lg w-full placeholder-gray-300" placeholder="متن خود را بنویسید . در اسرع وقت رسیدگی میشود" required></textarea>
                </div>
                @error('body')
                    <div class="text-red-500 text-sm mt-2">{{ $message }}</div>
                @enderror
            </div>
            <x-captcha/>
            <button type="submit" class="w-full py-3 text-white font-semibold bg-gradient-to-r from-blue-900 to-amber-600 hover:from-blue-800 hover:to-amber-500 transition-colors rounded-xl">ثبت</button>
        </form>
    </div>
@endsection
