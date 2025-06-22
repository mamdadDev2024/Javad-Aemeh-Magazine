@extends("layouts.default")
@section('title', " نوشتن پیشنهاد")
@section('body')
    <main class="flex justify-center py-8">
        <form action="{{ route('user.do.suggest') }}" enctype="multipart/form-data" method="post"
              class="container flex flex-col gap-6 p-8 bg-white shadow-lg rounded-lg max-w-lg w-full">
            @csrf
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700">عنوان مقاله</label>
                @error('title')
                    <div class="text-red-500 text-sm mt-1" id="error-title">{{ $message }}</div>
                @enderror
                <input value="{{ old('title') }}" type="text" name="title" id="title"
                       aria-describedby="error-title" required
                       class="mt-1 block w-full focus:outline-none transition-all border border-gray-300 rounded-md p-2 focus:ring focus:ring-blue-400">
            </div>
            <div>
                <label for="pdf" class="block text-sm font-medium text-gray-700">فایل مقاله به صورت (PDF)</label>
                @error('pdf')
                    <div class="text-red-500 text-sm mt-1" id="error-pdf">{{ $message }}</div>
                @enderror
                <input type="file" accept="application/pdf" name="pdf" id="pdf" aria-describedby="error-pdf"
                       required class="mt-1 block w-full focus:outline-none transition-all border border-gray-300 rounded-md p-2 focus:ring focus:ring-blue-400">
            </div>
            <div>
                <label for="word" class="block text-sm font-medium text-gray-700">فایل مقاله به صورت (Word)</label>
                @error('word')
                    <div class="text-red-500 text-sm mt-1" id="error-word">{{ $message }}</div>
                @enderror
                <input type="file" accept=".docx" name="word" id="word" aria-describedby="error-word"
                        class="mt-1 block w-full focus:outline-none transition-all border border-gray-300 rounded-md p-2 focus:ring focus:ring-blue-400">
            </div>
            <x-captcha/>
            <div>
                <button type="submit"
                        class="w-full bg-blue-500 text-white font-semibold p-2 rounded-md hover:bg-blue-600 transition duration-200">
                    ثبت
                </button>
            </div>
        </form>
    </main>
@endsection
