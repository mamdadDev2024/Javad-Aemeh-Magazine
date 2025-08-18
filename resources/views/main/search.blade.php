@extends('layouts.default')

@section('title', 'جست‌وجو')

@section('body')
<div class="container mx-auto p-4">
    <form method="GET"
          class="flex flex-col lg:flex-row items-center mx-auto w-full max-w-lg space-y-2 lg:space-y-0 lg:space-x-2"
          action="{{ route('search') }}">
        <input name="search" type="text" value="{{ request('search') }}"
               class="rounded lg:rounded-l-none px-3 py-2 w-full lg:w-64 focus:outline-none dark:bg-teal-700 dark:text-gray-200 bg-gray-200 text-gray-700 focus:ring-2 ring-blue-400 transition-all">

        <select name="type"
                class="w-full lg:w-auto px-3 py-2 bg-amber-500 text-white hover:bg-amber-600 focus:outline-none dark:bg-amber-600 dark:hover:bg-amber-500 transition-all">
            @php
                $types = [
                    "همه" => "all",
                    "نشریات" => "Magazine",
                    "رویداد ها" => "Event",
                    "مقالات" => "Article",
                    "اخبار" => "Khabar", // اصلاح "New" به "Khabar"
                ];
            @endphp
            @foreach($types as $key => $value)
                <option value="{{ $value }}" {{ request('type') == $value ? 'selected' : '' }}>
                    {{ $key }}
                </option>
            @endforeach
        </select>

        @error('type')
            <div class="text-red-500 text-sm mt-2">{{ $message }}</div>
        @enderror

        <button
            class="rounded lg:rounded-r-none px-4 py-2 w-full lg:w-auto bg-blue-500 text-white hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-500 transition-all">
            جستجو
        </button>
    </form>

    <h1 class="text-2xl font-semibold mb-4 text-center">پاسخ</h1>

    @if($results->isEmpty())
        <p class="text-center text-gray-600 dark:text-gray-400">هیچ محتوایی پیدا نشد.</p>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($results as $item)
                <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                    <!-- نمایش تصویر اگر وجود دارد -->
                    @php
                        $image = $item->image ?? null;
                    @endphp
                    @if($image)
                        <img src="{{ asset($image) }}" alt="تصویر {{ $item->title }}"
                             class="w-full h-48 object-cover rounded mb-4">
                    @endif

                    <h2 class="text-lg font-semibold mb-2">{{ $item->title }}</h2>

                    @php
                        $typeName = get_class($item);
                        $type_word = match ($typeName) {
                            "App\Models\Magazine" => "نشریه",
                            "App\Models\Event" => "رویداد",
                            "App\Models\Khabar" => "خبر",
                            "App\Models\Article" => "مقاله",
                            default => "محتوا"
                        };
                    @endphp

                    <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                        دسته‌بندی: {{ $item->category->name ?? 'بدون دسته' }} | نوع: {{ $type_word }}
                    </div>

                    <!-- نمایش خلاصه محتوا -->
                    <p class="text-gray-700 dark:text-gray-300">
                        {{ Str::limit($item->body ?? '...', 100) }}
                    </p>

                    <!-- ساخت لینک به صفحه جزئیات محتوا -->
                    @php
                        $routeName = match ($typeName) {
                            "App\Models\Magazine" => "Magazine.show",
                            "App\Models\Event" => "Event.show",
                            "App\Models\Khabar" => "Khabar.show",
                            "App\Models\Article" => "Article.show",
                            default => "Magazine.show"
                        };
                    @endphp

                    <a href="{{ route($routeName, $item->slug) }}"
                       class="text-blue-500 dark:text-blue-400 hover:underline mt-4 block">
                        بیشتر
                    </a>
                </div>
            @endforeach

            <div class="col-span-full flex justify-center mt-6">
                {{ $results->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
