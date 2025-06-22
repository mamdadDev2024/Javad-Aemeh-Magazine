@extends("layouts.default")
@section('title', "پنل کاربری")
@section('body')
@php
    try {
        $setting = Illuminate\Support\Facades\Storage::exists("settings.json")
            ? Illuminate\Support\Facades\Storage::get("settings.json")
            : json_encode(["activate" => false]);

        $json = json_decode($setting, true) ?? ["activate" => false];
    } catch (\Exception $e) {
        $json = ["activate" => false];
    }
@endphp
    <main class="flex justify-center py-8">
        <form action="{{ route('profile') }}" enctype="multipart/form-data" method="post" class="container flex flex-col gap-6 p-8 bg-white shadow-lg rounded-lg">
            @csrf
            @if(!empty($user['image']))
                <img src="{{ asset($user['image']) }}" alt="{{ $user['username'] ?? 'User' }}"
                     class="shadow-lg object-cover mx-auto shadow-black rounded-3xl h-64 w-64">
            @else
                <img src="{{ asset('images/default.png') }}" alt="Default User"
                     class="shadow-lg object-cover mx-auto shadow-black rounded-3xl h-64 w-64">
            @endif
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700">نام کاربری</label>
                <input disabled value="{{ $user['username'] ?? '' }}" type="text" id="username"
                       class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring focus:ring-blue-400">
            </div>
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">نام و نام خانوادگی</label>
                @error('name')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror
                <input value="{{ old('name', $user['name'] ?? '') }}" type="text" name="name" id="name"
                       class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring focus:ring-blue-400">
            </div>
            <div>
                <label for="number" class="block text-sm font-medium text-gray-700">شماره تماس</label>
                @error('number')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror
                <input value="{{ old('number', $user['number'] ?? '') }}" type="number" name="number" id="number"
                       class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring focus:ring-blue-400">
            </div>
            <div>
                <label for="image" class="block text-sm font-medium text-gray-700">نعویض عکس پروفایل</label>
                @error('image')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror
                <input type="file" accept="image/png,image/jpeg" name="image" id="image"
                       class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring focus:ring-blue-400">
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">ایمیل</label>
                @error('email')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror
                <input value="{{ old('email', $user['email'] ?? '') }}" type="email" name="email" id="email"
                       class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring focus:ring-blue-400">
            </div>
            @role("programmer")
            <div class="flex gap-2 items-center">
                <input type="checkbox" name="activate" id="activate"
                       {{ isset($json['activate']) && !$json['activate'] ? 'checked' : '' }}>
                <label for="activate" class="text-red-500 font-bold">غیر فعال کردن سایت</label>
            </div>
            @endrole
            @if(!Illuminate\Support\Facades\Auth::user()->hasRole("super admin"))
            <div class="flex gap-2 items-center">
                <input type="checkbox" value="{{ $user['id'] ?? '' }}" name="delete" id="delete">
                <label for="delete" class="text-red-500 font-bold">حذف حساب کاربری</label>
            </div>
            @endif
            <a href="{{ route('user.change.password') }}" class="text-sm font-medium text-blue-400 hover:text-blue-300">تغییر رمز عبور</a>
            <x-captcha/>
            <div>
                <button type="submit"
                        class="w-full bg-blue-500 text-white font-semibold p-2 rounded-md hover:bg-blue-600 transition duration-200">
                    ثبت
                </button>
            </div>
        </form>
    </main>
@endsection
