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

@push('scripts')
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
            <div class="article-form border rounded-md p-4 mb-4 bg-gray-100 dark:bg-gray-800 dark:border-gray-700">
                <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white">مقاله شماره ${newIndex + 1}</h3>
                <input type="hidden" name="articles[${newIndex}][id]" value="">
                <div class="mb-4">
                    <label for="articles_${newIndex}_title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">عنوان مقاله</label>
                    <input type="text" name="articles[${newIndex}][title]" id="articles_${newIndex}_title" class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md p-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100">
                </div>
                <div class="mb-6">
                    <label for="articles_${newIndex}_pdf" class="block text-sm font-medium text-gray-700 dark:text-gray-300">فایل مقاله</label>
                    <input type="file" name="articles[${newIndex}][addOn]" id="articles_${newIndex}_pdf" accept=".pdf,.docx" class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md p-3 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100">
                </div>
                <div class="mb-4">
                    <label for="articles_${newIndex}_author" class="block text-sm font-medium text-gray-700 dark:text-gray-300">نویسنده مقاله</label>
                    <input type="text" name="articles[${newIndex}][author]" id="articles_${newIndex}_author" class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md p-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100">
                </div>
                <div class="mb-4">
                    <label for="articles_${newIndex}_abstract" class="block text-sm font-medium text-gray-700 dark:text-gray-300">چکیده مقاله</label>
                    <textarea name="articles[${newIndex}][abstract]" id="articles_${newIndex}_abstract" class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md p-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100"></textarea>
                </div>
                <div class="mb-4">
                    <label for="articles_${newIndex}_body" class="block text-sm font-medium text-gray-700 dark:text-gray-300">متن مقاله</label>
                    <textarea name="articles[${newIndex}][body]" id="articles_${newIndex}_body" class="mt-1 block w-full h-36 border border-gray-300 dark:border-gray-600 rounded-md p-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100"></textarea>
                </div>
                <button type="button" class="remove-article bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded w-full sm:w-auto dark:bg-red-600 dark:hover:bg-red-700 transition duration-300"
                        onclick="this.closest('.article-form').remove(); resetArticleIndexes();">
                    حذف مقاله
                </button>
            </div>
            `;
            articlesContainer.insertAdjacentHTML('beforeend', template);
        });

        function resetArticleIndexes() {
            const articles = document.querySelectorAll('.article-form');
            console.log(articles);
            
            articles.forEach((article, index) => {
                // Update heading
                const h3 = article.querySelector('h3');
                if (h3) h3.textContent = `مقاله شماره ${index + 1}`;
                // Update hidden id
                const hiddenId = article.querySelector('input[type="hidden"]');
                if (hiddenId) hiddenId.name = `articles[${index}][id]`;
                // Update title
                const titleInput = article.querySelector('input[name*="title"]');
                if (titleInput) {
                    titleInput.name = `articles[${index}][title]`;
                    titleInput.id = `articles_${index}_title`;
                    const titleLabel = article.querySelector('label[for*="title"]');
                    if (titleLabel) titleLabel.setAttribute('for', `articles_${index}_title`);
                }
                // Update addOn
                const addOnInput = article.querySelector('input[name*="addOn"]');
                if (addOnInput) {
                    addOnInput.name = `articles[${index}][addOn]`;
                    addOnInput.id = `articles_${index}_pdf`;
                    const addOnLabel = article.querySelector('label[for*="pdf"]');
                    if (addOnLabel) addOnLabel.setAttribute('for', `articles_${index}_pdf`);
                }
                // Update author
                const authorInput = article.querySelector('input[name*="author"]');
                if (authorInput) {
                    authorInput.name = `articles[${index}][author]`;
                    authorInput.id = `articles_${index}_author`;
                    const authorLabel = article.querySelector('label[for*="author"]');
                    if (authorLabel) authorLabel.setAttribute('for', `articles_${index}_author`);
                }
                // Update abstract
                const abstractTextarea = article.querySelector('textarea[name*="abstract"]');
                if (abstractTextarea) {
                    abstractTextarea.name = `articles[${index}][abstract]`;
                    abstractTextarea.id = `articles_${index}_abstract`;
                    const abstractLabel = article.querySelector('label[for*="abstract"]');
                    if (abstractLabel) abstractLabel.setAttribute('for', `articles_${index}_abstract`);
                }
                // Update body
                const bodyTextarea = article.querySelector('textarea[name*="body"]');
                if (bodyTextarea) {
                    bodyTextarea.name = `articles[${index}][body]`;
                    bodyTextarea.id = `articles_${index}_body`;
                    const bodyLabel = article.querySelector('label[for*="body"]');
                    if (bodyLabel) bodyLabel.setAttribute('for', `articles_${index}_body`);
                }
            });
        }
    });
</script>
@endpush
