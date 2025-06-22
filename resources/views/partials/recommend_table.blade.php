<div class="container mx-auto mb-8">
    @if ($items->isNotEmpty())
        <table class="w-full table-auto text-sm sm:text-base border-collapse">
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-2">نویسنده</th>
                    <th class="p-2">عنوان</th>
                    <th class="p-2">تاریخ</th>
                    <th class="p-2">PDF</th>
                    <th class="p-2">WORD</th>
                    <th class="p-2">حذف</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $item)
                    @php
                        $type = match (get_class($item)) {
                            'App\\Models\\article' => 'نشریه',
                            'App\\Models\\event' => 'رویداد',
                            'App\\Models\\khabar' => 'خبر',
                            default => 'نشریه',
                        };
                    @endphp
                    <tr class="border-b">
                        <td class="p-2">{{ $item->user->username }}</td>
                        <td class="p-2">
                            {{ $item->title }}
                        </td>
                        <td class="p-2">
                            {{ Illuminate\Support\Carbon::parse($item->created_at)->diffForHumans() }}
                        </td>
                        <td class="p-2">

                            <a href="{{ route('download', ["url" => $item->pdf]) }}"
                                class="text-blue-500 hover:underline">دانلود فایل PDF</a>
                        </td>
                        <td class="p-2">
                            @empty($item->word)
                                <p>وجود ندارد</p>
                            @else
                                <a href="{{ route('download', ["url" => $item->word]) }}"
                                    class="text-blue-500 hover:underline">دانلود فایل WORD</a>
                            @endempty
                        </td>
                        <td class="p-2">
                            <form action="{{ route('admin.recommend.delete', $item->id) }}" method="POST"
                                  class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="px-3 py-1 bg-red-500 text-white rounded-md hover:bg-red-600">
                                    حذف
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4">
            {{ $items->links() }}
        </div>
    @else
        <div class="bg-yellow-100 text-yellow-800 text-center py-4 rounded-md">
            {{ $title }} موجود نیست
        </div>
    @endif
</div>
