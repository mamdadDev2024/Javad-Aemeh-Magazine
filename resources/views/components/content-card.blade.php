<div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow dark:bg-gray-800">
    <img src="{{ asset($item->image ?? 'path/to/default-image.jpg') }}" alt="{{ $item->title }}"
        class="w-full h-40 object-cover" />
    <div class="p-4">
        <a href="{{ route("$type.show", $item->id) }}"
            class="text-lg font-bold text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-500">
            {{ Str::limit($item->title, 20) }}
        </a>
        <p class="text-gray-600 mt-2 text-sm dark:text-gray-300">
            {{ Str::limit($item->body, 60) }}</p>
        <div class="flex items-center justify-between mt-4">
            <span class="text-xs text-gray-500 dark:text-gray-400">تاریخ:
                {{ $item->created_at->format('Y/m/d') }}</span>
            <a href="{{ route('toggle_like', ['type' => $type, 'id' => $item->id]) }}"
                class="text-red-500 text-sm dark:text-red-400">
                ❤️ {{ $item->likers()->count() }}
            </a>
        </div>
    </div>
</div>
