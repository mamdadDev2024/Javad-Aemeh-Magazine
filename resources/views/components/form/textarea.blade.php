@props(['name', 'label', 'value' => '', 'class' => '', 'required' => false, 'rows' => 3])

<div class="mb-4">
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ $label }}</label>
    <textarea name="{{ $name }}"
              id="{{ $name }}"
              rows="{{ $rows }}"
              @if($required) required @endif
              {{ $attributes->merge(['class' => 'mt-1 block w-full border rounded-md p-3 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 dark:border-gray-600 ' . $class]) }}
    >{{ old($name, $value) }}</textarea>
    @error($name)
        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
    @enderror
</div>
