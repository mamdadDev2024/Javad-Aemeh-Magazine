@extends('layouts.default')

@section('title', 'نشریات')

@section('body')
    <main class="w-full p-5">
        <div class="grid p-4 gap-4 rounded-2xl bg-blue-300 dark:bg-darkPrimary xl:grid-cols-5 lg:grid-cols-4 md:grid-cols-3 sm:grid-cols-2 max-sm:grid-cols-1">

            @foreach ($news as $new)
                <div
                    class="col-span-1 p-4 rounded-xl bg-blue-400 dark:bg-emerald-800 shadow-md transition-transform transform hover:scale-105 hover:shadow-lg duration-300 animate-fadeIn">
                    <a href="{{ route('Khabar.show', $new->slug) }}">
                    <img src="{{ asset($new->image) }}" alt="{{ $new->title }}"
                        class="w-full h-48 rounded-xl object-cover mb-4" loading="lazy">
                    <a href="{{ route('Khabar.show', $new->slug) }}"
                       class="text-lg font-bold text-lightText dark:text-darkText hover:text-secondary transition-colors">
                        {{ Illuminate\Support\Str::limit($new->title, 40) }}
                    </a>
                    <p class="text-sm mt-2 text-lightText/80 dark:text-darkText/70">
                        نویسنده: {{ $new->user->username }}
                    </p>
                    <div class="flex justify-between mt-4 text-sm text-lightText/70 dark:text-darkText/60">
                        <p>بازدیدها: {{ $new->views_count }}</p>
                        <p>لایک‌ها: {{ $new->likes_count }}</p>
                    </div>
                </div>
            @endforeach
            <div class="col-span-full flex justify-center mt-6">
                {{ $news->links() }}
            </div>
        </div>
    </main>
@endsection
