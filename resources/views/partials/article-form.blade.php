@php
    $articleTitle    = data_get($article, 'title', '');
    $articleAbstract = data_get($article, 'abstract', '');
    $articleAddOn    = data_get($article, 'url', '');
    $articleText     = data_get($article, 'body', '');
    $articleAuthor   = data_get($article, 'author', '');
@endphp

<div class="article-form border rounded-md p-4 mb-4 bg-gray-100 dark:bg-gray-800 dark:border-gray-700">
    <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-100">مقاله شماره {{ $index + 1 }}</h3>

    <!-- عنوان مقاله -->
    <div class="mb-4">
        <input type="hidden" name="articles[{{ $index }}][id]" value="{{ data_get($article, 'id', '') }}">
        <label for="articles_{{ $index }}_title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">عنوان مقاله</label>
        <input
            type="text"
            name="articles[{{ $index }}][title]"
            id="articles_{{ $index }}_title"
            value="{{ old("articles.{$index}.title", $articleTitle) }}"
            class="mt-1 block w-full border rounded-md p-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 dark:border-gray-600 @error("articles.{$index}.title") border-red-500 @enderror"
        >
        @error("articles.{$index}.title")
            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
        @enderror
    </div>

    <!-- فایل مقاله -->
    <div class="mb-6">
        <label for="articles_{{ $index }}_pdf" class="block text-sm font-medium text-gray-700 dark:text-gray-300">فایل مقاله</label>
        <input
            type="file"
            name="articles[{{ $index }}][addOn]"
            id="articles_{{ $index }}_pdf"
            accept=".docx,.pdf"
            class="mt-1 block w-full border rounded-md p-3 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 border-gray-300 dark:border-gray-600"
        >
        @if ($articleAddOn)
            <input type="hidden" name="articles[{{ $index }}][existing_file]" value="{{ $articleAddOn }}">
        @endif
        @error("articles.{$index}.addOn")
            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
        @enderror
    </div>

    <!-- نویسنده مقاله -->
    <div class="mb-4">
        <label for="articles_{{ $index }}_author" class="block text-sm font-medium text-gray-700 dark:text-gray-300">نویسنده مقاله</label>
        <input
            name="articles[{{ $index }}][author]"
            id="articles_{{ $index }}_author"
            value="{{ old("articles.{$index}.author", $articleAuthor) }}"
            class="mt-1 block w-full border rounded-md p-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 dark:border-gray-600 @error("articles.{$index}.author") border-red-500 @enderror"
        >
        @error("articles.{$index}.author")
            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
        @enderror
    </div>

    <!-- چکیده مقاله -->
    <div class="mb-4">
        <label for="articles_{{ $index }}_abstract" class="block text-sm font-medium text-gray-700 dark:text-gray-300">چکیده مقاله</label>
        <textarea
            name="articles[{{ $index }}][abstract]"
            id="articles_{{ $index }}_abstract"
            class="mt-1 block w-full border rounded-md p-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 dark:border-gray-600 @error("articles.{$index}.abstract") border-red-500 @enderror"
            placeholder="چکیده مقاله را بنویسید"
        >{{ old("articles.{$index}.abstract", $articleAbstract) }}</textarea>
        @error("articles.{$index}.abstract")
            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
        @enderror
    </div>

    <!-- متن مقاله -->
    <div class="mb-4">
        <label for="articles_{{ $index }}_body" class="block text-sm font-medium text-gray-700 dark:text-gray-300">متن مقاله</label>
        <textarea
            name="articles[{{ $index }}][body]"
            id="articles_{{ $index }}_body"
            class="mt-1 block h-36 w-full border rounded-md p-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 dark:border-gray-600 @error("articles.{$index}.body") border-red-500 @enderror"
        >{{ old("articles.{$index}.body", $articleText) }}</textarea>
        @error("articles.{$index}.body")
            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
        @enderror
    </div>

    <button
        type="button"
        onclick="this.closest('.article-form').remove(); resetArticleIndexes();"
        class="remove-article bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded dark:bg-red-600 dark:hover:bg-red-700"
    >
        حذف مقاله
    </button>
</div>

@push('scripts')
<script>
function resetArticleIndexes() {
    const forms = document.querySelectorAll('#articles-container .article-form');
    forms.forEach((form, index) => {
        form.querySelector('h3').innerText = `مقاله شماره ${index + 1}`;
        form.querySelectorAll('input, textarea').forEach(el => {
            if(el.type === 'file') return;
            const name = el.getAttribute('name');
            if (name) el.setAttribute('name', name.replace(/articles\[\d+\]/, `articles[${index}]`));
        });
    });
}
</script>
@endpush
