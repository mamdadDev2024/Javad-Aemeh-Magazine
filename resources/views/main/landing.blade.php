@extends('layouts.default')

@section('title', 'موسسه جواد الأئمه علیه السلام')

@section("styles")
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
@endsection

@section('body')
<main class="w-full transition-all flex flex-col">

    <!-- خبرها -->
    <a href="{{ route('news') }}" class="text-4xl hover:text-blue-600 font-bold mx-auto my-4 dark:text-white">خبرها</a>
    <div class="rounded-2xl mx-4">
        <div class="rounded-xl p-4 flex flex-col lg:flex-row justify-between bg-blue-300 dark:bg-darkPrimary items-center shadow-lg">
            @if (count($khabars) > 0)
                <x-swiper_container :items="$khabars" type="Khabar" defaultLink="{{ route('news') }}" containerClass="news-swiper-container" />
            @endif
        </div>
    </div>

    <a href="{{ route('events') }}" class="text-4xl hover:text-blue-600 font-bold mx-auto my-4 dark:text-white">رویدادها</a>
    <div class="rounded-2xl mx-4">
        <div class="rounded-xl p-4 flex flex-col lg:flex-row justify-between bg-blue-300 dark:bg-darkPrimary items-center shadow-lg">
            @if (count($events) > 0)
                <x-swiper_container :items="$events" type="Event" defaultLink="{{ route('events') }}" containerClass="event-swiper-container" />
            @endif
        </div>
    </div>

    <a href="{{ route('magazines') }}" class="text-4xl hover:text-blue-600 font-bold mx-auto my-4 dark:text-white">نشریات</a>
    <div class="rounded-2xl px-3 py-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
        <div class="relative rounded-xl p-6 flex flex-col lg:col-span-4 lg:flex-row bg-blue-300 dark:bg-darkPrimary items-center shadow-lg">
            @if (isset($magazines) && count($magazines) > 0)
                <x-swiper_container :items="$magazines" type="Magazine" defaultLink="{{ route('magazines') }}" containerClass="magazine-swiper-container " />
            @endif
        </div>
        <div class="p-4 dark:text-white sm:grid-cols-1 rounded-xl bg-gray-400 dark:bg-slate-700 shadow-lg">
            <h3 class="text-xl font-bold mb-3">راهنمایی</h3>
            <p class="text-sm">
                {{ $sections['magazineGuide']['content'] ?? 'راهنمایی موجود نیست' }}
            </p>
        </div>
    </div>

</main>
@endsection
@section("scripts")
<script>
    document.addEventListener("DOMContentLoaded", function () {
    const extraSlides = document.querySelectorAll(".extra-slide");
    if (window.innerWidth <= 768) {
        extraSlides.forEach((slide) => {
            slide.remove();
        });
    }
    window.addEventListener("resize", () => {
        if (window.innerWidth <= 768) {
            extraSlides.forEach((slide) => {
                slide.remove();
            });
        }
    });
});
</script>
@endsection
