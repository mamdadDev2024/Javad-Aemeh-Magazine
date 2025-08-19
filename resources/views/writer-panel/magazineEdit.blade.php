@extends('layouts.default')

@section('title', 'ویرایش نشریه')
@section('body')

<div class="container mx-auto px-4 py-8" data-aos="fade-up">
    <h1 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white">ویرایش نشریه</h1>

    <form action="{{ route('writer.magazine.do.update') }}" id="myForm" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" value="{{ $Magazine->id }}" name="id">
        @method('PUT')

        {{-- عنوان --}}
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">عنوان نشریه</label>
            <input type="text" name="title" value="{{ old('title', $Magazine->title) }}"
                class="mt-1 block w-full border rounded-md p-3 border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white @error('title') border-red-500 @enderror" required>
            @error('title')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
        </div>

        {{-- توضیحات --}}
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">توضیحات نشریه</label>
            <input type="text" name="body" value="{{ old('body', $Magazine->body) }}"
                class="mt-1 block w-full border rounded-md p-3 border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white @error('body') border-red-500 @enderror" required>
            @error('body')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
        </div>

        {{-- تصویر --}}
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">تصویر نشریه</label>
            <input type="file" name="image" accept="image/*" class="mt-1 block w-full border rounded-md p-3 border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
            @if ($Magazine->image)
                <div class="mt-2">
                    <img src="{{ asset($Magazine->image) }}" class="w-24 h-24 rounded-lg object-cover" id="preview-image">
                </div>
            @endif
            @error('image')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
        </div>

        {{-- فایل PDF/Word --}}
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">فایل ضمیمه نشریه</label>
            <input type="file" name="addOn" accept=".pdf,.docx"
                   class="mt-1 block w-full border rounded-md p-3 border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
            @if ($Magazine->pdf)
                <a href="{{ route('download', ['url' => $Magazine->pdf]) }}" class="text-blue-500 hover:underline">دانلود فایل PDF</a>
            @endif
            @error('addOn')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
        </div>

        {{-- دسته‌بندی‌ها --}}
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">دسته‌بندی‌ها</label>
            <select name="category[]" multiple
                    class="mt-1 block w-full border rounded-md p-3 border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                @foreach ($categories as $category)
                    <option value="{{ $category['id'] }}" {{ in_array($category['id'], old('category', $Magazine->categories->pluck('id')->toArray())) ? 'selected' : '' }}>
                        {{ $category['name'] }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- مقالات --}}
        <div id="articles-container">
            @foreach ($Magazine->articles as $index => $article)
                @include('partials.article-form', ['index' => $index, 'article' => $article])
            @endforeach
        </div>

        <button type="button" id="add-article-button"
                class="bg-blue-600 text-white px-4 py-2 rounded mt-2 w-full sm:w-auto hover:bg-blue-700 transition">
            افزودن مقاله جدید
        </button>

        <x-captcha/>

        <div class="mt-6">
            <button type="submit"
                    class="bg-green-600 text-white px-6 py-2 rounded w-full sm:w-auto hover:bg-green-700 transition">
                بروزرسانی نشریه
            </button>
        </div>
    </form>
</div>

{{-- Overlay لودینگ --}}
<div id="overlay" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50">
    <div class="loader border-4 border-t-4 border-white rounded-full w-12 h-12 animate-spin"></div>
</div>

@endsection

@section('scripts')
<script>
    // Overlay لودینگ هنگام ارسال فرم
    document.getElementById("myForm").addEventListener("submit", function() {
      document.getElementById("overlay").classList.remove("hidden");
    });

    // افزودن مقاله جدید
    document.addEventListener('DOMContentLoaded', () => {
        const addArticleButton = document.getElementById('add-article-button');
        const articlesContainer = document.getElementById('articles-container');

        addArticleButton.addEventListener('click', () => {
            const newIndex = articlesContainer.querySelectorAll('.article-form').length;

            const template = `
            <div class="article-form border rounded-md p-4 mb-4 bg-gray-100 dark:bg-gray-800">
                <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white">مقاله شماره ${newIndex + 1}</h3>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">عنوان مقاله</label>
                    <input type="text" name="articles[${newIndex}][title]" class="mt-1 block w-full border rounded-md p-2 dark:bg-gray-700 dark:text-white">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">چکیده مقاله</label>
                    <textarea name="articles[${newIndex}][abstract]" class="mt-1 block w-full border rounded-md p-2 dark:bg-gray-700 dark:text-white"></textarea>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">متن مقاله</label>
                    <textarea name="articles[${newIndex}][text]" class="mt-1 block w-full h-36 border rounded-md p-2 dark:bg-gray-700 dark:text-white"></textarea>
                </div>

                <button type="button" class="remove-article bg-red-500 text-white px-4 py-2 rounded w-full sm:w-auto hover:bg-red-600 transition"
                        onclick="this.closest('.article-form').remove();">
                    حذف مقاله
                </button>
            </div>
            `;
            articlesContainer.insertAdjacentHTML('beforeend', template);
        });
    });
</script>
@endsection
