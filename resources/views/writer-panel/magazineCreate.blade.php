@extends('layouts.default')

@section('title', 'ایجاد نشریه')
@section('body')

    <!-- AOS Animation -->
    <div class="container mx-auto px-4 py-8" data-aos="fade-up">
        <h1 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white">ایجاد نشریه جدید</h1>

        <form action="{{ route('writer.magazine.do.create') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-6">
                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">عنوان نشریه</label>
                <input type="text" name="title" id="title" value="{{ old('title') }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md p-3 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-800 dark:text-white @error('title') border-red-500 @enderror"
                    placeholder="عنوان نشریه را وارد کنید" required>
                @error('title')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-6">
                <label for="desc" class="block text-sm font-medium text-gray-700 dark:text-gray-300">توضیحات نشریه</label>
                <input type="text" name="desc" id="desc" value="{{ old('desc') }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md p-3 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-800 dark:text-white @error('desc') border-red-500 @enderror"
                    placeholder="توضیحات نشریه را وارد کنید" required>
                @error('desc')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-6">
                <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300">(فقط JPEG و JPG)تصویر نشریه</label>
                <input type="file" name="image" id="image" accept="image/jpg, image/jpeg"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md p-3 dark:bg-gray-800 dark:text-white @error('image') border-red-500 @enderror"
                    required>
                @error('image')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-6">
                <label for="addOn" class="block text-sm font-medium text-gray-700 dark:text-gray-300">فایل نشریه به صورت (PDF)
                    یا (DOCX)</label>
                <input type="file" name="addOn" id="addOn" accept=".docx,.pdf"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md p-3 dark:bg-gray-800 dark:text-white @error('addOn') border-red-500 @enderror"
                    required>
                @error('addOn')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-6">
                <label for="category"
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">دسته‌بندی‌ها</label>
                <select name="category[]" id="category" multiple
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md p-3 dark:bg-gray-800 dark:text-white @error('category') border-red-500 @enderror">
                    @foreach ($categories as $category)
                        <option value="{{ $category['id'] }}"
                            {{ in_array($category['id'], old('category', [])) ? 'selected' : '' }}>
                            {{ $category['name'] }}
                        </option>
                    @endforeach
                </select>
                @error('category')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div id="articles-container">
                @if (old('articles'))
                    @foreach (old('articles') as $index => $article)
                        @include('partials.article-form', ['index' => $index, 'article' => $article])
                    @endforeach
                @else
                    @include('partials.article-form', ['index' => 0, 'article' => null])
                @endif
            </div>

            <button type="button" id="add-article-button"
                class="bg-blue-600 text-white px-4 py-2 rounded mt-2 hover:bg-blue-700 focus:outline-none transition duration-300">
                افزودن مقاله جدید
            </button>

            <x-captcha/>
            <div class="mt-6">
                <button type="submit"
                    class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700 focus:outline-none transition duration-300">ثبت
                    نشریه</button>
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
    <div class="article-form border rounded-md p-4 mb-4 bg-gray-100">
        <h3 class="text-lg font-semibold mb-4">مقاله شماره ${articleIndex + 1}</h3>
        <div class="mb-4">
            <label for="articles[${articleIndex}][title]" class="block text-sm font-medium text-gray-700">عنوان مقاله</label>
            <input type="text" name="articles[${articleIndex}][title]" id="articles_${articleIndex}_title"
                   class="mt-1 block w-full border rounded-md p-2 @error('articles.${articleIndex}.title') border-red-500 @enderror"
                   placeholder="عنوان مقاله را وارد کنید">
            @error('articles.${articleIndex}.title')
                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>
                <div class="mb-4">
            <label for="articles[${articleIndex}][author]" class="block text-sm font-medium text-gray-700">نویسنده مقاله</label>
            <input name="articles[${articleIndex}][author]" id="articles_${articleIndex}_author"
                      class="mt-1 block w-full border rounded-md p-2 @error('articles.${articleIndex}.author') border-red-500 @enderror"
                      placeholder="نویسنده مقاله را بنویسید">
            @error('articles.${articleIndex}.author')
                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-4">
            <label for="articles[${articleIndex}][abstract]" class="block text-sm font-medium text-gray-700">چکیده مقاله</label>
            <textarea name="articles[${articleIndex}][abstract]" id="articles_${articleIndex}_abstract"
                      class="mt-1 block w-full border rounded-md p-2 @error('articles.${articleIndex}.abstract') border-red-500 @enderror"
                      placeholder="چکیده مقاله را بنویسید"></textarea>
            @error('articles.${articleIndex}.abstract')
                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-4">
            <label for="articles[${articleIndex}][text]" class="block text-sm font-medium text-gray-700">متن مقاله</label>
            <textarea name="articles[${articleIndex}][text]" id="articles_${articleIndex}_text"
                      class="mt-1 block w-full border rounded-md p-2 @error('articles.${articleIndex}.text') border-red-500 @enderror"
                      placeholder="متن مقاله را بنویسید"></textarea>
            @error('articles.${articleIndex}.text')
                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>
        <button type="button" class="remove-article bg-red-500 text-white px-4 py-2 rounded"
                onclick="this.closest('.article-form').remove();articleIndex -= 1">
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


    <!-- AOS Script -->
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>
    <script>
        AOS.init({
            disable: false,
            startEvent: 'DOMContentLoaded',
            initClassName: 'aos-init',
            animatedClassName: 'aos-animate',
            useClassNames: false,
            disableMutationObserver: false,
            debounceDelay: 50,
            throttleDelay: 99,
        });
    </script>
@endsection
