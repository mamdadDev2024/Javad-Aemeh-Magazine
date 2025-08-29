<div class="container mx-auto mb-8">
    @if ($items->isNotEmpty())
        <table class="w-full table-auto text-sm sm:text-base border-collapse rounded-lg overflow-hidden shadow">
            <thead>
                <tr class="bg-gray-200 text-gray-700">
                    <th class="p-2">عکس</th>
                    <th class="p-2">نویسنده</th>
                    <th class="p-2">سطح</th>
                    <th class="p-2">عنوان</th>
                    <th class="p-2 hidden sm:table-cell">متن</th>
                    <th class="p-2">تاریخ</th>
                    <th class="p-2">عملیات</th>
                    <th class="p-2">حذف</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $item)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-2 text-center">
                            <img src="{{ $item->image ? asset( $item->image) : asset('images/placeholder.png') }}"
                                 alt="{{ \Illuminate\Support\Str::limit($item->title, 40) }} - image"
                                 class="h-12 w-12 sm:h-16 sm:w-16 rounded-full object-cover mx-auto">
                        </td>
                        <td class="p-2 text-center">{{ $item->user->username ?? 'ناشناس' }}</td>
                        <td class="p-2 text-center">{{ $item->scope ?? '-' }}</td>
                        <td class="p-2">{{ $item->title }}</td>
                        <td class="p-2 hidden sm:table-cell">
                            {{ \Illuminate\Support\Str::limit($item->body, 50) }}
                        </td>
                        <td class="p-2 text-center">
                            {{ \Illuminate\Support\Carbon::parse($item->created_at)->diffForHumans() }}
                        </td>
                        <td class="p-2 text-center">
                            <a href="{{ route($editRoute, $item->slug) }}"
                               class="px-3 py-1 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition">
                                ویرایش
                            </a>
                        </td>
                        <td class="p-2 text-center">
                            <form action="{{ route($deleteRoute, $item->id) }}" method="POST"
                                  onsubmit="return confirm('آیا مطمئن هستید؟')" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="px-3 py-1 bg-red-500 text-white rounded-md hover:bg-red-600 transition">
                                    حذف
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4">
            {{ $items->appends(request()->query())->links() }}
        </div>
    @else
        <div class="bg-yellow-100 text-yellow-800 text-center py-4 rounded-md">
            {{ $title ?? 'موردی' }} موجود نیست
        </div>
    @endif
</div>
