@extends('layouts.default')

@section('title', 'ایجاد نشریه')
@section('body')

<div class="container mx-auto px-4 py-8" data-aos="fade-up">
    <h1 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white">ایجاد نشریه جدید</h1>

    <form action="{{ route('writer.magazine.do.create') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- عنوان --}}
        <div class="mb-6">
            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">عنوان نشریه</label>
            <input type="text" name="title" id="title" value="{{ old('title') }}"
                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md p-3 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-800 dark:text-white @error('title') border-red-500 @enderror"
                placeholder="عنوان نشریه را وارد کنید" required>
            @error('title')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
        </div>

        {{-- توضیحات --}}
        <div class="mb-6">
            <label for="desc" class="block text-sm font-medium text-gray-700 dark:text-gray-300">توضیحات نشریه</label>
            <input type="text" name="desc" id="desc" value="{{ old('desc') }}"
                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md p-3 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-800 dark:text-white @error('desc') border-red-500 @enderror"
                placeholder="توضیحات نشریه را وارد کنید" required>
            @error('desc')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
        </div>

        {{-- تصویر نشریه --}}
        <div class="mb-6">
            <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300">(JPEG یا JPG) تصویر نشریه</label>
            <input type="file" name="image" id="image" accept="image/image"
                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md p-3 dark:bg-gray-800 dark:text-white @error('image') border-red-500 @enderror"
                required>
            @error('image')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
        </div>

        {{-- فایل PDF/DOCX --}}
        <div class="mb-6">
            <label for="addOn" class="block text-sm font-medium text-gray-700 dark:text-gray-300">فایل نشریه (PDF یا DOCX)</label>
            <input type="file" name="addOn" id="addOn" accept=".pdf,.docx"
                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md p-3 dark:bg-gray-800 dark:text-white @error('addOn') border-red-500 @enderror"
                required>
            @error('addOn')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
        </div>

        {{-- دسته‌بندی‌ها --}}
        <div class="mb-6">
            <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300">دسته‌بندی‌ها</label>
            <select name="category[]" id="category" multiple
                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md p-3 dark:bg-gray-800 dark:text-white @error('category') border-red-500 @enderror">
                @foreach ($categories as $category)
                    <option value="{{ $category['id'] }}" {{ in_array($category['id'], old('category', [])) ? 'selected' : '' }}>
                        {{ $category['name'] }}
                    </option>
                @endforeach
            </select>
            @error('category')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
        </div>

        {{-- مقالات --}}
        <div id="articles-container">
            @if(old('articles'))
                @foreach(old('articles') as $index => $article)
                    @include('partials.article-form', ['index' => $index, 'article' => $article])
                @endforeach
            @else
                @include('partials.article-form', ['index' => 0, 'article' => null])
            @endif
        </div>

        <button type="button" id="add-article-button"
            class="bg-blue-600 text-white px-4 py-2 rounded mt-2 w-full sm:w-auto hover:bg-blue-700 focus:outline-none transition duration-300">
            افزودن مقاله جدید
        </button>

        <x-captcha/>

        <div class="mt-6">
            <button type="submit"
                class="bg-green-600 text-white px-6 py-2 rounded w-full sm:w-auto hover:bg-green-700 focus:outline-none transition duration-300">
                ثبت نشریه
            </button>
        </div>
    </form>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    let articleIndex = {{ old('articles') ? count(old('articles')) : 1 }};
    const addArticleButton = document.getElementById('add-article-button');
    const articlesContainer = document.getElementById('articles-container');

    addArticleButton.addEventListener('click', () => {
        const template = `
        <div class="article-form border rounded-md p-4 mb-4 bg-gray-100 dark:bg-gray-800">
            <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white">مقاله شماره ${articleIndex + 1}</h3>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">عنوان مقاله</label>
                <input type="text" name="articles[${articleIndex}][title]"
                    class="mt-1 block w-full border rounded-md p-2 dark:bg-gray-700 dark:text-white" placeholder="عنوان مقاله">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">نویسنده مقاله</label>
                <input type="text" name="articles[${articleIndex}][author]"
                    class="mt-1 block w-full border rounded-md p-2 dark:bg-gray-700 dark:text-white" placeholder="نویسنده مقاله">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">چکیده مقاله</label>
                <textarea name="articles[${articleIndex}][abstract]"
                    class="mt-1 block w-full border rounded-md p-2 dark:bg-gray-700 dark:text-white" placeholder="چکیده مقاله"></textarea>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">متن مقاله</label>
                <textarea name="articles[${articleIndex}][text]"
                    class="mt-1 block w-full border rounded-md p-2 dark:bg-gray-700 dark:text-white" placeholder="متن مقاله"></textarea>
            </div>

            <button type="button" class="remove-article bg-red-500 text-white px-4 py-2 rounded w-full sm:w-auto hover:bg-red-600 transition"
                onclick="this.closest('.article-form').remove()">
                حذف مقاله
            </button>
        </div>
        `;
        const container = document.createElement('div');
        container.innerHTML = template.trim();
        articlesContainer.appendChild(container.firstChild);
        articleIndex++;
    });
});
</script>

<!-- AOS -->
<script src="https://unpkg.com/aos@next/dist/aos.js"></script>
<script>
    AOS.init({ duration: 600, easing: 'ease-in-out', once: true });
</script>
@endsection
