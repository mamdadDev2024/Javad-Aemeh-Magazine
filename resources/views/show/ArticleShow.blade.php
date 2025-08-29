@extends('layouts.default')

@section('title', $item->title)

@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
@endsection

@section('body')
    <div class="min-h-screen p-5 bg-gray-100 dark:bg-gray-900">
        <div class="max-w-4xl mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
            <div class="p-5">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100" data-aos="fade-right">
                    {{ $item->title }}
                </h1>
                <div class="flex justify-between articles-center mt-2">
                    <span class="text-sm text-gray-500 dark:text-gray-400">
                        نویسنده: {{ $item->author }}
                    </span>
                    <span class="text-sm text-gray-500 dark:text-gray-400">
                        تعداد نظرات: {{ $item->comments->count() }}
                    </span>
                </div>
                <p class="mt-4 text-gray-700 dark:text-gray-300" data-aos="fade-up">
                    <span class=" font-bold text-lg">چکیده</span><br>
                    {!! nl2br(e($item->abstract)) !!}
                </p>
                <p class="mt-4 text-right text-gray-700 dark:text-gray-300" data-aos="fade-up">
                    <span class=" font-bold text-lg">متن</span><br>
                    <!-- CHANGED: Use body instead of text to match schema -->
                    {!! nl2br(e($item->body)) !!}
                </p>
                <!-- Like and Views -->
                <div class="flex articles-center justify-between mt-5">
                    <span class="text-sm text-gray-500 dark:text-gray-400">
                        بازدید: {{ $item->views_count }}
                    </span>
                    <form method="POST" action="{{ route('toggle.like', ['id' => $item->id, 'type' => strtolower($type)]) }}">
                        @csrf
                        <button class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
                            لایک: {{ $item->like_count }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Comments Section -->
        <section id="comments" class="max-w-4xl mx-auto mt-10 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-5"
            data-aos="fade-left">
            <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">نظرات</h2>
            @forelse ($item->comments as $comment)
                <div class="mt-4 p-4 bg-gray-100 dark:bg-gray-700 rounded-lg">
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $comment->created_at->diffForHumans() }}</p>
                    <!-- CHANGED: Align with body column -->
                    <p class="mt-2 text-gray-900 dark:text-gray-100">{{ $comment->body }}</p>
                </div>
            @empty
                <p class="text-gray-500 dark:text-gray-400 mt-5">هیچ نظری وجود ندارد.</p>
            @endforelse

            @auth
                <form method="POST" action="{{ route('create.comment', ["contentId" => $item->id , "model" => $type]) }}" class="mt-5">
                    @csrf
                    <!-- CHANGED: Align request field name with backend -->
                    <textarea name="body" rows="3" class="w-full p-3 rounded-lg dark:bg-gray-700 dark:text-gray-300"
                        placeholder="نظر خود را وارد کنید..." required></textarea>
                    <x-captcha/>
                    <button class="mt-3 px-5 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
                        ارسال
                    </button>
                </form>
            @else
                <p class="text-center mt-5">
                    <a href="{{ route('login') }}" class="text-blue-500 hover:underline">ورود برای ثبت نظر</a>
                </p>
            @endauth
        </section>

        <!-- Related Articles Section -->
        <section class="max-w-4xl mx-auto mt-10 p-5 bg-white dark:bg-gray-800 rounded-xl shadow-lg" data-aos="fade-up">
            <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">محتواهای مشابه</h2>
            <div class="mt-5 space-y-4">
                @forelse ($relateds as $related)
                    <div class="flex articles-center bg-gray-100 dark:bg-gray-700 p-3 rounded-lg">
                        <!-- CHANGED: Use storage path for related images -->
                        <img src="{{ asset($related['image']) }}" alt="{{ $related['title'] }}"
                            class="w-16 h-16 object-cover rounded-lg">
                        <div class="ml-4">
                            <!-- CHANGED: Build route name dynamically in PHP to avoid literal string -->
                            <a href="{{ route($type.'.show', $related['slug']) }}"
                                class="text-blue-500 dark:text-orange-300 hover:underline">
                                {{ Illuminate\Support\Str::limit($related['title'], 30) }}
                            </a>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                {{ Illuminate\Support\Str::limit($related['body'], 50) }}
                            </p>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 dark:text-gray-400">محتوای مشابهی یافت نشد.</p>
                @endforelse
            </div>
        </section>
    </div>
@endsection
@section('scripts')
    <script>
        AOS.init({
            once: true,
            duration: 800,
        });
    </script>
@endsection
