@extends('layouts.default')

@section('title', 'موسسه جواد الأئمه علیه السلام')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
@endsection

@section('body')
<main class="w-full transition-all flex flex-col gap-8">

    {{-- بخش خبرها --}}
    <section class="mx-4">
        <a href="{{ route('news') }}" class="text-4xl font-bold hover:text-blue-600 dark:text-white block mb-4 text-center">خبرها</a>
        <div class="rounded-xl p-4 bg-blue-300 dark:bg-darkPrimary shadow-lg">
            @if($khabars->isNotEmpty())
                <x-swiper_container :items="$khabars" type="Khabar" defaultLink="{{ route('news') }}" containerClass="news-swiper-container" />
            @else
                <p class="text-center text-gray-500 dark:text-gray-400">خبری موجود نیست</p>
            @endif
        </div>
    </section>

    {{-- بخش رویدادها --}}
    <section class="mx-4">
        <a href="{{ route('events') }}" class="text-4xl font-bold hover:text-blue-600 dark:text-white block mb-4 text-center">رویدادها</a>
        <div class="rounded-xl p-4 bg-blue-300 dark:bg-darkPrimary shadow-lg">
            @if($events->isNotEmpty())
                <x-swiper_container :items="$events" type="Event" defaultLink="{{ route('events') }}" containerClass="event-swiper-container" />
            @else
                <p class="text-center text-gray-500 dark:text-gray-400">رویدادی موجود نیست</p>
            @endif
        </div>
    </section>

    {{-- بخش نشریات --}}
    <section class="mx-4">
        <a href="{{ route('magazines') }}" class="text-4xl font-bold hover:text-blue-600 dark:text-white block mb-4 text-center">نشریات</a>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">

            {{-- اسلایدر اصلی نشریات --}}
            <div class="relative rounded-xl p-6 flex flex-col lg:col-span-4 lg:flex-row bg-blue-300 dark:bg-darkPrimary items-center shadow-lg">
                @if(isset($magazines) && $magazines->isNotEmpty())
                    <x-swiper_container :items="$magazines" type="Magazine" defaultLink="{{ route('magazines') }}" containerClass="magazine-swiper-container" />
                @else
                    <p class="text-center text-gray-500 dark:text-gray-400 w-full">نشریه‌ای موجود نیست</p>
                @endif
            </div>

            {{-- بخش راهنمایی --}}
            <div class="p-4 dark:text-white rounded-xl bg-gray-400 dark:bg-slate-700 shadow-lg">
                <h3 class="text-xl font-bold mb-3">راهنمایی</h3>
                <p class="text-sm">
                    {{ $sections['magazineGuide']['content'] ?? 'راهنمایی موجود نیست' }}
                </p>
            </div>
        </div>
    </section>

</main>
@endsection

