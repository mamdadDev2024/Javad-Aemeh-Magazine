<div class="container mx-auto mb-8">
    @if ($items->isNotEmpty())
        <table class="w-full table-auto text-sm sm:text-base border-collapse">
            <thead>
                <tr class="bg-gray-200">
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
                    @php
                        $scope = $item->scope() ?? null;
                    @endphp
                    <tr class="border-b">
                        <td class="p-2">
                            <img src="{{ asset($item->image) }}" alt="{{ Illuminate\Support\Str::limit($item->title, 40) }} - image"
                                 class="h-12 w-12 sm:h-16 sm:w-16 rounded-full object-cover">
                        </td>
                        <td class="p-2">{{ $item->user->username }}</td>
                        <td class="p-2">{{ $scope->toArray ?? "..." }}</td>
                        <td class="p-2">{{ $item->title }}</td>
                        <td class="p-2 hidden sm:table-cell">
                            {{ Illuminate\Support\Str::limit($item->body, 50) }}
                        </td>
                        <td class="p-2">
                            {{ Illuminate\Support\Carbon::parse($item->created_at)->diffForHumans() }}
                        </td>
                        <td class="p-2">
                            <a href="{{ route('writer.new.edit', $item->slug) }}"
                               class="px-3 py-1 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                                ویرایش
                            </a>
                        </td>
                        <td class="p-2">
                            <form action="{{ route('admin.new.delete', $item->id) }}" method="POST"
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
