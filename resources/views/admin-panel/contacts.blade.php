@extends("layouts.default")

@section("title", "تماس با ما های بی پاسخ")

@section('body')
<main class="p-6">
    <div class="flex justify-between items-center w-full h-12 mb-4">
        <h1 class="text-2xl font-bold">لیست فرم‌ها</h1>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full border-collapse border border-gray-300 shadow-lg">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border border-gray-300 p-2">نویسنده</th>
                    <th class="border border-gray-300 p-2">متن</th>
                    <th class="border border-gray-300 p-2">شماره تماس</th>
                    <th class="border border-gray-300 p-2">تاریخ</th>
                    <th class="border border-gray-300 p-2">حذف</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($contacts as $contact)
                    <tr class="hover:bg-gray-50 transition duration-300">
                        <td class="border border-gray-300 p-2">{{ $contact->user?->username ?? 'ناشناس' }}</td>
                        <td class="border border-gray-300 p-2">{{ $contact->body }}</td>
                        <td class="border border-gray-300 p-2">{{ $contact->phone }}</td>
                        <td class="border border-gray-300 p-2">{{ $contact->created_at->diffForHumans() }}</td>
                        <td class="border border-gray-300 p-2">
                            <form action="{{ route('admin.contact.delete', $contact->id) }}" method="POST" class="inline-block"
                                  onsubmit="return confirm('آیا مطمئن هستید که می‌خواهید این نظر را حذف کنید؟');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 rounded-xl px-4 py-1 text-white transition duration-300 hover:bg-red-600">حذف</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4 mx-auto max-w-4xl">
        {{ $contacts->links() }}
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const buttons = document.querySelectorAll('.toggle-reply');

        buttons.forEach(button => {
            button.addEventListener('click', function () {
                const contactId = this.getAttribute('data-contact-id');
                const replyForm = document.getElementById('reply-form-' + contactId);

                if (replyForm) {
                    replyForm.classList.toggle('hidden');
                    // Toggle max-height for animation
                    if (replyForm.classList.contains('hidden')) {
                        replyForm.style.maxHeight = null; // Reset height when hidden
                    } else {
                        replyForm.style.maxHeight = replyForm.scrollHeight + 'px'; // Set height when showing
                    }
                }
            });
        });
    });
</script>

<style>
    .reply-form {
        transition: max-height 0.5s ease;
        overflow: hidden;
    }
</style>
@endsection
