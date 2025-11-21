@extends('layouts.default')

@section('title', 'ویرایش نشریه')
@section('body')

<div class="container mx-auto px-4 py-8" data-aos="fade-up">
    <h1 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white">ویرایش نشریه</h1>

    <form action="{{ route('writer.magazine.do.update') }}" id="myForm" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <input type="hidden" value="{{ $Magazine->id }}" name="id">

        {{-- عنوان --}}
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">عنوان نشریه</label>
            <input type="text" name="title"
                value="{{ old('title', $Magazine->title) }}"
                class="mt-1 block w-full border rounded-md p-3 border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white @error('title') border-red-500 @enderror"
                required>
            @error('title') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
        </div>

        {{-- توضیحات --}}
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">توضیحات نشریه</label>
            <textarea name="body" rows="3"
                class="mt-1 block w-full border rounded-md p-3 border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white @error('body') border-red-500 @enderror"
                required>{{ old('body', $Magazine->body) }}</textarea>
            @error('body') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
        </div>

        {{-- تصویر --}}
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">تصویر نشریه</label>
            <input type="file" name="image" accept="image/*"
                   id="image-input"
                   class="mt-1 block w-full border rounded-md p-3 border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white">

            <div class="mt-3">
                <img src="{{ asset($Magazine->image) }}"
                     class="w-24 h-24 rounded-lg object-cover"
                     id="preview-image">
            </div>

            @error('image') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
        </div>

        {{-- فایل PDF/Word --}}
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">فایل ضمیمه نشریه</label>
            <input type="file" name="addOn" accept=".pdf,.docx"
                class="mt-1 block w-full border rounded-md p-3 border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white">

            @if ($Magazine->pdf)
                <a href="{{ route('download', ['url' => $Magazine->pdf]) }}"
                   class="text-blue-500 hover:underline mt-2 block">دانلود فایل فعلی</a>
            @endif

            @error('addOn') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
        </div>

        {{-- دسته‌بندی‌ها --}}
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">دسته‌بندی‌ها</label>
            <select name="category[]" multiple
                class="mt-1 block w-full border rounded-md p-3 border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                @foreach($categories as $category)
                    <option value="{{ $category['id'] }}"
                        {{ in_array($category['id'], old('category', $Magazine->categories->pluck('id')->toArray())) ? 'selected' : '' }}>
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

{{-- Overlay --}}
<div id="overlay" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50">
    <div class="loader border-4 border-t-4 border-white rounded-full w-12 h-12 animate-spin"></div>
</div>

@endsection

@section('scripts')
<script>
function resetArticleIndexes () {
    const forms = document.querySelectorAll('#articles-container .article-form');
    forms.forEach((form, index) => {
        const heading = form.querySelector('h3');
        if (heading) {
            heading.innerText = `مقاله شماره ${index + 1}`;
        }

        form.querySelectorAll('input, textarea, select').forEach(el => {
            const name = el.getAttribute('name');
            const id = el.getAttribute('id');

            if (name && name.includes('articles[')) {
                if (el.type === 'file' || name.includes('existing_file')) return;

                const newName = name.replace(/articles\[\d+\]/, `articles[${index}]`);
                el.setAttribute('name', newName);
            }

            if (id && id.includes('articles_')) {
                const newId = id.replace(/articles_\d+_/, `articles_${index}_`);
                el.setAttribute('id', newId);

                const label = form.querySelector(`label[for="${id}"]`);
                if (label) {
                    label.setAttribute('for', newId);
                }
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    let articleIndex = {{ old('articles') ? count(old('articles')) : 1 }};
    if (articleIndex === 0) articleIndex = 1;
    const addArticleButton = document.getElementById('add-article-button');
    const articlesContainer = document.getElementById('articles-container');

    function createArticleForm(index) {
        const div = document.createElement('div');
        div.className = 'article-form border rounded-md p-4 mb-4 bg-gray-100 dark:bg-gray-800 dark:border-gray-700';
        div.innerHTML = `
            <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white">مقاله شماره ${index + 1}</h3>
            <input type="hidden" name="articles[${index}][id]" value="">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">عنوان مقاله</label>
                <input type="text" name="articles[${index}][title]" class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md p-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100" placeholder="عنوان مقاله">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">نویسنده مقاله</label>
                <input type="text" name="articles[${index}][author]" class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md p-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100" placeholder="نویسنده مقاله">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">فایل مقاله</label>
                <input type="file" name="articles[${index}][addOn]" accept=".pdf,.docx" class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md p-3 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">چکیده مقاله</label>
                <textarea name="articles[${index}][abstract]" class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md p-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100" placeholder="چکیده مقاله"></textarea>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">متن مقاله</label>
                <textarea name="articles[${index}][body]" class="mt-1 block w-full h-36 border border-gray-300 dark:border-gray-600 rounded-md p-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100" placeholder="متن مقاله"></textarea>
            </div>
            <button type="button" class="remove-article bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded w-full sm:w-auto dark:bg-red-600 dark:hover:bg-red-700 transition duration-300" onclick="this.closest('.article-form').remove(); resetArticleIndexes();">
                حذف مقاله
            </button>
        `;
        return div;
    }

    addArticleButton.addEventListener('click', () => {
        articlesContainer.appendChild(createArticleForm(articleIndex));
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
