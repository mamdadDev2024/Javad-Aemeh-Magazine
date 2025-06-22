@extends("layouts.default")
@section('title', "ویرایش نشریه")
@section('body')
    <main class="flex justify-center py-8">
        <form action="{{ route('writer.new.do.update') }}" enctype="multipart/form-data" method="POST" class="container flex flex-col gap-6 p-8 bg-white shadow-lg rounded-lg">
            @csrf
            <input type="hidden" name="id" value="{{$Khabar->id}}">
            @method('PUT')
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700">عنوان مقاله</label>
                @error('title')
                    <div class="text-red-500 text-sm mt-1" id="error-title">{{ $message }}</div>
                @enderror
                <input value="{{ old('title', $Khabar->title) }}" type="text" name="title" id="title" aria-describedby="error-title" class="mt-1 block w-full focus:outline-none transition-all border border-gray-300 rounded-md p-2 focus:ring focus:ring-blue-400" required>
            </div>
            <div>
                <label for="category" class="block text-sm font-medium text-gray-700 mt-1">دسته بندی</label>
                @error('category')
                    <div class="text-red-500 text-sm mt-1" id="error-category">{{ $message }}</div>
                @enderror
                <select name="category[]" id="category" aria-describedby="error-category" multiple class="rounded-lg w-72 border p-2" required>
                    <option value="" disabled {{ $Khabar->categories->isEmpty() ? 'selected' : '' }}>هیچ دسته بندی</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category['id'] }}" {{ in_array($category['id'], $Khabar->categories->pluck('id')->toArray()) ? 'selected' : '' }}>
                            {{ $category['name'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="image" class="block text-sm font-medium text-gray-700">عکس</label>
                @error('image')
                    <div class="text-red-500 text-sm mt-1" id="error-image">{{ $message }}</div>
                @enderror
                <input type="file"accept="image/jpg, image/jpeg" name="image" id="image" aria-describedby="error-image" class="mt-1 block w-full focus:outline-none transition-all border border-gray-300 rounded-md p-2 focus:ring focus:ring-blue-400">
                @if ($Khabar->image)
                    <img src="{{ asset($Khabar->image) }}" alt="تصویر فعلی" class="mt-2 w-24 object-cover h-24 rounded-lg">
                @endif
            </div>
            <div>
                <label for="pdf" class="block text-sm font-medium text-gray-700">فایل ضمیمه</label>
                @error('pdf')
                    <div class="text-red-500 text-sm mt-1" id="error-pdf">{{ $message }}</div>
                @enderror
                <input type="file" accept="application/pdf" name="pdf" id="pdf" aria-describedby="error-pdf" class="mt-1 block w-full focus:outline-none transition-all border border-gray-300 rounded-md p-2 focus:ring focus:ring-blue-400">
                @if ($Khabar->pdf)
                    <a href="{{route("download" , $Khabar->pdf)}}" target="_blank" class="mt-2 text-blue-600 underline">دانلود فایل ضمیمه</a>
                @endif
            </div>
            <div>
                <label for="body" class="block text-sm font-medium text-gray-700">متن مقاله</label>
                @error('body')
                    <div class="text-red-500 text-sm mt-1" id="error-body">{{ $message }}</div>
                @enderror
                <textarea name="body" id="body" aria-describedby="error-body" class="mt-1 block w-full min-h-20 max-h-96 focus:outline-none transition-all border border-gray-300 rounded-md p-2 focus:ring focus:ring-blue-400" required>{{ old('body', $Khabar->body) }}</textarea>
            </div>
            <x-captcha/>
            <div>
                <button type="submit" class="w-full bg-blue-500 text-white font-semibold p-2 rounded-md hover:bg-blue-600 transition duration-200">ذخیره تغییرات</button>
            </div>
        </form>
    </main>
@endsection
