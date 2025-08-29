@props(['name', 'label', 'accept' => '', 'existingFile' => null, 'class' => ''])

<div class="mb-6">
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ $label }}</label>
    <input type="file"
           name="{{ $name }}"
           id="{{ $name }}"
           accept="{{ $accept }}"
           {{ $attributes->merge(['class' => 'mt-1 block w-full border rounded-md p-3 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 dark:border-gray-600 ' . $class]) }}
    >
    @if($existingFile)
        <p class="text-sm mt-1 text-gray-600 dark:text-gray-400">
            فایل فعلی: <a href="{{ $existingFile }}" target="_blank" class="underline">{{ basename($existingFile) }}</a>
        </p>
        <input type="hidden" name="{{ $name }}_existing" value="{{ $existingFile }}">
    @endif
    @error($name)
        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
    @enderror
</div>
