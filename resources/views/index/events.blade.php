@extends('layouts.default')

@section('title', 'نشریات')

@section('body')
    <main class="w-full p-5">
        <div
            class="grid gap-4  p-4 rounded-2xl bg-blue-300 dark:bg-darkPrimary xl:grid-cols-5 lg:grid-cols-4 md:grid-cols-3 sm:grid-cols-2 max-sm:grid-cols-1">

            @foreach ($events as $event)
                <div
                    class="col-span-1  p-4 rounded-xl bg-blue-400 dark:bg-emerald-800 shadow-md transition-transform transform hover:scale-105 hover:shadow-lg duration-300 animate-fadeIn">
                    <a href="{{ route('Event.show', $event->slug) }}">
                    <img src="{{ asset($event->image) }}" alt="{{ $event->title }}"
                        class="w-full h-48 rounded-xl object-cover mb-4" loading="lazy">
                    </a>
                    <a href="{{ route('Event.show', $event->slug) }}"
                        class="text-lg font-bold text-lightText dark:text-darkText hover:text-secondary transition-colors">
                        {{ Illuminate\Support\Str::limit($event->title, 40) }}
                    </a>
                    <p class="text-sm mt-2 text-lightText/80 dark:text-darkText/70">
                        نویسنده: {{ $event->user->username }}
                    </p>
                    <div class="flex justify-between mt-4 text-sm text-lightText/70 dark:text-darkText/60">
                        <p>بازدیدها: {{ $event->views_count }}</p>
                        <p>لایک‌ها: {{ $event->likes_count }}</p>
                    </div>
                </div>
            @endforeach
            <div class="col-span-full flex justify-center mt-6">
                {{ $events->links() }}
            </div>
        </div>
    </main>
@endsection
