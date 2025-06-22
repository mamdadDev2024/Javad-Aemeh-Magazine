@extends('layouts.default')

@section('title', $magazine->title)

@section('body')
    <div class="min-h-screen p-5 bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200">
        <div class="max-w-3xl mx-auto bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
            <div class="relative">
                <img src="{{ asset($magazine->image) }}" alt="{{ $magazine->title }}" class=" w-full object-cover"
                    data-aos="fade-down">
                <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent flex items-end p-5">
                    <h1 class="text-3xl font-bold text-white" data-aos="fade-right">{{ $magazine->title }}</h1>
                </div>
            </div>
            <div class="p-6">
                <div class="flex justify-between items-center text-sm mb-4">
                    <span>✍ نویسنده: <strong>{{ $magazine->user->username }}</strong></span>
                    <span>💬 نظرات: {{ $magazine->comments->count() }}</span>
                </div>
                <p class="mt-4 text-gray-700 flex my-3 justify-center text-right dark:text-gray-300" data-aos="fade-up">
                    {!! nl2br(e($magazine->body)) !!}
                </p>
                @if ($magazine->pdf)
                    <div class="p-4 bg-blue-50 dark:bg-blue-900 rounded-lg">
                        <h3 class="text-lg font-bold">📂 فایل‌ نشریه</h3>
                        <a href="{{ route('download', ["url"=>$magazine->pdf]) }}"
                            class="text-blue-500 hover:text-blue-700 dark:hover:text-blue-300">دانلود فایل</a>
                    </div>
                @endif
                <div class="mt-6">
                    <h3 class="text-xl font-bold mb-3">📜 مقالات</h3>
                    <ul class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg space-y-2">
                        @foreach ($magazine->articles as $index => $article)
                            <li class=" flex justify-between text-right items-center">
                                <div class=" flex items-center gap-4">
                                    <p class=" mx-3 my-3">{!!$index  = $index + 1 !!}</p>
                                    <a href="{{ route('Article.show', ['Article' => $article->slug]) }}"
                                        class="text-blue-500 dark:text-orange-300 hover:underline">
                                        {{ $article->title }}
                                    </a>
                                </div>
                            @if ($article->url)
                            <a href="{{route("download" , ["url" => $article->url]) }}" class=" rounded-2xl bg-blue-500 px-4 text-conter py-2 max-md:py-1 max-md:px-3 text-white h-9">
                                دانلود
                            </a>
                            @endif
                            </li>
                            <hr>
                        @endforeach
                    </ul>
                </div>
                <div class="flex justify-between items-center mt-6">
                    <span>👁 بازدید: {{ $magazine->views_count }}</span>
                    <form method="POST" action="{{ route('toggle.like', ['id' => $magazine->id, 'type' => $type]) }}">
                        @csrf
                        <button
                            class="flex items-center px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
                            ❤️ لایک: {{ $magazine->like_count }}
                        </button>
                    </form>
                </div>
                <div class="flex items-center justify-center mt-5">
                    <p class=" text-bold text-lg mr-2">برچسب ها</p>
                    @forelse ($categories as $category)
                        <p class=" text-sm text-blue-500 underline mx-2 my-1">#{{$category["name"]}}</p>
                    @empty
                        <p class=" text-sm text-blue-500 underline mx-2 my-1">هیچ دسته بندی انتخاب نشده</p>
                    @endforelse
                </div>
            </div>
        </div>
        <section id="comments" class="max-w-4xl mx-auto mt-10 bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6"
            data-aos="fade-left">
            <h2 class="text-2xl font-bold mb-4">💬 نظرات</h2>
            @forelse ($magazine->comments as $comment)
                <div class="mt-4 p-4 bg-gray-100 dark:bg-gray-700 rounded-lg">
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $comment->created_at->diffForHumans() }}</p>
                    <p class="mt-2">{{ $comment->text }}</p>
                </div>
            @empty
                <p class="text-gray-500 dark:text-gray-400">هیچ نظری وجود ندارد.</p>
            @endforelse

            @auth
                <form method="POST"
                    action="{{ route('create.comment', ['contentId' => $magazine->id, 'model' => 'Magazine']) }}"
                    class="mt-6">
                    @csrf
                    <textarea name="text" rows="3" class="w-full p-3 bg-gray-100 dark:bg-gray-700 rounded-lg resize-none"
                        placeholder="نظر خود را وارد کنید..." required></textarea>
                    <x-captcha/>
                    <button class="mt-3 w-full px-5 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
                        ارسال نظر
                    </button>
                </form>
            @else
                <p class="text-center mt-5">
                    <a href="{{ route('login') }}" class="text-blue-500 hover:underline">ورود برای ثبت نظر</a>
                </p>
            @endauth
        </section>
        <section class="max-w-4xl mx-auto mt-10 bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6" data-aos="fade-up">
            <h2 class="text-2xl font-bold mb-4">🔗 محتوای مشابه</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @forelse ($relateds as $related)
                    <div
                        class="flex items-center bg-gray-100 dark:bg-gray-700 p-4 rounded-lg shadow-sm hover:shadow-md transition">
                        <img src="{{ asset($related['image']) }}" alt="{{ $related['title'] }}"
                            class="w-16 h-16 object-cover rounded-lg">
                        <div class="ml-4 truncate">
                            <a href="{{ route("$type.show", $related['slug']) }}"
                                class="text-blue-500 dark:text-orange-300 hover:underline">
                                {{ Illuminate\Support\Str::limit($related['title'], 40) }}
                            </a>
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
        AOS.init();
    </script>
@endsection
