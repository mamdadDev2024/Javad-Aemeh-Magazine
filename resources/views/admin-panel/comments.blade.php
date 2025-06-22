@extends("layouts.default")
@section("title", "نظرات تایید نشده")
@section('body')
    <main class="p-6">
        <div class="flex justify-between items-center w-full h-12">
            <h1 class="text-2xl font-bold">لیست نظرات</h1>
            <a href="{{ route('admin.comment.accept.all') }}" class="rounded-2xl bg-red-600 text-white px-4 py-2 w-28 text-center transition duration-300 hover:bg-red-700">
                تایید همه
            </a>
        </div>
        <table class="w-full overflow-x-auto border-collapse border border-gray-300">
            <thead>
                <tr>
                    <th class="border border-gray-300 p-2">نویسنده</th>
                    <th class="border border-gray-300 p-2">محتوای مربوطه</th>
                    <th class="border border-gray-300 p-2">متن</th>
                    <th class="border border-gray-300 p-2">تاریخ</th>
                    <th class="border border-gray-300 p-2">تایید</th>
                    <th class="border border-gray-300 p-2">حذف</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($notConfirmedComments as $comment)
                    <tr>
                        <td class="border border-gray-300 p-2">{{ $comment->user->username }}</td>
                        <td class="border border-gray-300 p-2">{{ $comment->commentable->title }}</td>
                        <td class="border border-gray-300 p-2">{{ $comment->text }}</td>
                        <td class="border border-gray-300 p-2">{{ Illuminate\Support\Carbon::parse($comment->created_at)->diffForHumans() }}</td>
                        <td class="border border-gray-300 p-2">
                            <form action="{{route("admin.comment.accept" , $comment->id)}}" method="post">
                                @csrf
                                <button class="bg-green-500 rounded-xl px-4 py-1 ">تایید</button>
                            </form>
                        </td>
                        <td class="border border-gray-300 p-2">
                            <form action="{{ route('admin.comment.delete', $comment->id) }}" method="POST" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 rounded-xl px-4 py-1 ">حذف</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4 mx-auto max-w-4xl">
            {{ $notConfirmedComments->links() }} <!-- Pagination -->
        </div>
    </main>
@endsection
