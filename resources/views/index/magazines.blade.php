@extends('layouts.default')

@section('title', 'نشریات')

@section('body')
    <main class="w-full p-5">
        <div class="grid gap-4 p-4 rounded-2xl bg-blue-300 dark:bg-darkPrimary xl:grid-cols-5 lg:grid-cols-4 md:grid-cols-3 sm:grid-cols-2 max-sm:grid-cols-1">
            @foreach ($magazines as $magazine)
                <div
                    class="col-span-1 p-4 rounded-xl bg-blue-400 dark:bg-emerald-800 shadow-md transition-transform transform hover:scale-105 hover:shadow-lg duration-300 animate-fadeIn">
                    <a href="{{ route('Magazine.show', $magazine->slug) }}">
                        <img src="{{ asset($magazine->image) }}" alt="{{ $magazine->title }}"
                            class="w-full h-48 rounded-xl object-cover mb-4" loading="lazy">
                    </a>
                    <a href="{{ route('Magazine.show', $magazine->slug) }}"
                       class="text-lg font-bold text-lightText dark:text-darkText hover:text-secondary transition-colors">
                        {{ Illuminate\Support\Str::limit($magazine->title, 40) }}
                    </a>
                    <p class="text-sm mt-2 text-lightText/80 dark:text-darkText/70">
                        نویسنده: {{ $magazine->user->username }}
                    </p>
                    <div class="flex justify-between mt-4 text-sm text-lightText/70 dark:text-darkText/60">
                        <p>بازدیدها: {{ $magazine->views_count }}</p>
                        <p>لایک‌ها: {{ $magazine->likes_count }}</p>
                    </div>
                </div>
            @endforeach
            <div class="col-span-full flex justify-center mt-6">
                {{ $magazines->links() }}
            </div>
        </div>
    </main>
@endsection
