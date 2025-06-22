@extends('layouts.default')

@section('title', 'جست‌وجو')

@section('body')
<div class="container mx-auto p-4">
    <form method="GET" class="flex flex-col lg:flex-row items-center mx-auto w-full max-w-lg space-y-2 lg:space-y-0 lg:space-x-2" action="{{ route('search') }}">
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
                    "اخبار" => "New",
                ];
            @endphp
            @foreach($types as $key => $type)
                <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                    {{ $key }}
                </option>
            @endforeach
        </select>

        <!-- نمایش خطاها در صورت وجود -->
        @error('type')
            <div class="text-red-500 text-sm mt-2">{{ $message }}</div>
        @enderror

        <button
            class="rounded lg:rounded-r-none px-4 py-2 w-full lg:w-auto bg-blue-500 text-white hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-500 transition-all">جستجو</button>
    </form>

    <!-- نمایش عنوان جستجو -->
    <h1 class="text-2xl font-semibold mb-4 text-center">پاسخ</h1>

    <!-- نمایش پیام در صورت عدم وجود نتایج -->
    @if($results->isEmpty())
        <p class="text-center text-gray-600 dark:text-gray-400">هیچ محتوایی پیدا نشد.</p>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($results as $item)
                <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                    <h2 class="text-lg font-semibold mb-2">{{ $item->title }}</h2>
                    @php
                        $typeName = get_class($item);
                        $type_word = match ($typeName) {
                            "App\Models\Magazine" => "نشریه",
                            "App\Models\Event" => "رویداد",
                            "App\Models\Khabar" => "خبر",
                            default => "نشریه"
                        };
                    @endphp
                    <div class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        دسته بندی: {{ $item->category->name ?? 'هیچ' }} | نوع: {{ ucfirst($type_word) }}
                    </div>

                    <!-- نمایش محتوای محدود شده -->
                    <p class="text-gray-700 dark:text-gray-300">
                        {{ Str::limit($item->content, 100) }}
                    </p>

                    <!-- ساخت لینک به صفحه جزئیات محتوا -->
                    @php
                        $type = match (get_class($item)) {
                            "App\Models\Magazine" => "Magazine.show",
                            "App\Models\Event" => "Event.show",
                            "App\Models\Khabar" => "Khabar.show",
                            default => "Magazine.show"
                        };
                    @endphp

                    <a href="{{ route($type, $item->slug) }}" class="text-blue-500 dark:text-blue-400 hover:underline mt-4 block">
                        بیشتر
                    </a>
                </div>
            @endforeach
        </div>
    @endif
</div>

@endsection
