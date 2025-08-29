<div class="container mx-auto mb-8">
    @if ($items->isNotEmpty())
        {{-- Desktop Table --}}
        <div class="hidden sm:block">
            <table class="w-full table-auto text-sm sm:text-base border-collapse rounded-lg overflow-hidden shadow bg-white dark:bg-gray-800">
                <thead>
                    <tr class="bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-200">
                        @if($showImage ?? false)
                            <th class="p-2">عکس</th>
                        @endif
                        <th class="p-2">نویسنده</th>
                        <th class="p-2">عنوان</th>
                        @if($showBody ?? false)
                            <th class="p-2">متن</th>
                        @endif
                        <th class="p-2">تاریخ</th>
                        @if($showFiles ?? false)
                            <th class="p-2">PDF</th>
                            <th class="p-2">WORD</th>
                        @endif
                        <th class="p-2">عملیات</th>
                        <th class="p-2">حذف</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $item)
                        <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                            @if($showImage ?? false)
                                <td class="p-2">
                                    <img src="{{ $item->image ? asset( $item->image) : asset('images/placeholder.png') }}"
                                         alt="{{ $item->title }} - image"
                                         class="h-12 w-12 sm:h-16 sm:w-16 rounded-full object-cover">
                                </td>
                            @endif
                            <td class="p-2">{{ $item->user->username ?? 'ناشناس' }}</td>
                            <td class="p-2">{{ $item->title }}</td>
                            @if($showBody ?? false)
                                <td class="p-2">{{ Illuminate\Support\Str::limit($item->body, 40) }}</td>
                            @endif
                            <td class="p-2">{{ \Illuminate\Support\Carbon::parse($item->created_at)->diffForHumans() }}</td>
                            @if($showFiles ?? false)
                                <td class="p-2">
                                    @empty($item->pdf)
                                        <p>وجود ندارد</p>
                                    @else
                                        <a href="{{ route('download', ['url' => $item->pdf]) }}" class="text-blue-500 hover:underline">PDF</a>
                                    @endempty
                                </td>
                                <td class="p-2">
                                    @empty($item->word)
                                        <p>وجود ندارد</p>
                                    @else
                                        <a href="{{ route('download', ['url' => $item->word]) }}" class="text-blue-500 hover:underline">WORD</a>
                                    @endempty
                                </td>
                            @endif
                            <td class="p-2">
                                <a href="{{ route($editRoute, $item->slug) }}" class="px-3 py-1 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition">ویرایش</a>
                            </td>
                            <td class="p-2 text-center">
                                <form action="{{ route($deleteRoute, $item->id) }}" method="POST" onsubmit="return confirm('آیا مطمئن هستید؟')" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1 bg-red-500 text-white rounded-md hover:bg-red-600 transition">حذف</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Mobile Cards --}}
        <div class="sm:hidden space-y-4">
            @foreach ($items as $item)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 flex flex-col space-y-2">
                    @if($showImage ?? false)
                        <img src="{{ $item->image ? asset( $item->image) : asset('images/placeholder.png') }}"
                             alt="{{ $item->title }} - image"
                             class="h-24 w-24 rounded-full object-cover mx-auto">
                    @endif
                    <div class="text-gray-800 dark:text-gray-100 font-semibold text-center">{{ $item->title }}</div>
                    <div class="text-gray-600 dark:text-gray-300 text-sm text-center">نویسنده: {{ $item->user->username ?? 'ناشناس' }}</div>
                    @if($showBody ?? false)
                        <div class="text-gray-700 dark:text-gray-200 text-sm">{{ Illuminate\Support\Str::limit($item->body, 60) }}</div>
                    @endif
                    <div class="text-gray-500 dark:text-gray-400 text-sm">تاریخ: {{ \Illuminate\Support\Carbon::parse($item->created_at)->diffForHumans() }}</div>
                    @if($showFiles ?? false)
                        <div class="flex justify-around space-x-2 rtl:space-x-reverse">
                            @empty($item->pdf)
                                <span class="text-gray-400 text-sm">PDF: ندارد</span>
                            @else
                                <a href="{{ route('download', ['url' => $item->pdf]) }}" class="text-blue-500 text-sm hover:underline">PDF</a>
                            @endempty
                            @empty($item->word)
                                <span class="text-gray-400 text-sm">WORD: ندارد</span>
                            @else
                                <a href="{{ route('download', ['url' => $item->word]) }}" class="text-blue-500 text-sm hover:underline">WORD</a>
                            @endempty
                        </div>
                    @endif
                    <div class="flex justify-between mt-2">
                        <a href="{{ route($editRoute, $item->slug) }}" class="px-3 py-1 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition text-sm w-1/2 text-center">ویرایش</a>
                        <form action="{{ route($deleteRoute, $item->id) }}" method="POST" onsubmit="return confirm('آیا مطمئن هستید؟')" class="w-1/2">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full px-3 py-1 bg-red-500 text-white rounded-md hover:bg-red-600 transition text-sm">حذف</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $items->appends(request()->query())->links() }}
        </div>
    @else
        <div class="bg-yellow-100 dark:bg-yellow-700 text-yellow-800 dark:text-yellow-100 text-center py-4 rounded-md">
            {{ $title ?? 'موردی' }} موجود نیست
        </div>
    @endif
</div>
