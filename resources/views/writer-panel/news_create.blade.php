@extends("layouts.default")
@section('title', "ایجاد خبر")
@section('body')
    <main class="flex justify-center py-8">
        <form action="{{ route('writer.new.do.create') }}" enctype="multipart/form-data" method="POST" class="container flex flex-col gap-6 p-8 bg-white shadow-lg rounded-lg">
            @csrf
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700">عنوان خبر</label>
                @error('title')
                    <div class="text-red-500 text-sm mt-1" id="error-title">{{ $message }}</div>
                @enderror
                <input value="{{ old('title') }}" type="text" name="title" id="title" aria-describedby="error-title" class="mt-1 block w-full focus:outline-none transition-all border border-gray-300 rounded-md p-2 focus:ring focus:ring-blue-400">
            </div>
            <div class=" grid justify-items-center grid-cols-2 max-lg:grid-cols-1 max-lg:gap-2 lg:gap-5">
                <div class=" col-span-1">
                    <label for="scope" class="block text-sm font-medium text-gray-700 mt-1">سطح خبر</label>
                    @error('scope')
                        <div class="text-red-500 text-sm mt-1" id="error-scope">{{ $message }}</div>
                    @enderror
                    <select name="scope"class="rounded-lg max-lg:min-w-48 lg:min-w-72 border p-2">
                        @php $first = true; @endphp

                        @foreach($scopes as $key => $scope)
                            <option value="{{ $key }}"
                                {{ (in_array($key, old('scope', [])) || $first) ? 'selected' : '' }}>
                                {{ $scope }}
                            </option>
                            @php $first = false; @endphp
                        @endforeach
                    </select>
                </div>
                <div class=" col-span-1">
                    <label for="category" class="block text-sm font-medium text-gray-700 mt-1">دسته بندی</label>
                    @error('category')
                        <div class="text-red-500 text-sm mt-1" id="error-category">{{ $message }}</div>
                    @enderror
                    <select name="category[]" id="category" aria-describedby="error-category" multiple class="rounded-lg max-lg:min-w-48 lg:min-w-72 border p-2">
                        <option value="" disabled {{ old('category') ? '' : 'selected' }}>هیچ دسته بندی</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category['id'] }}" {{ in_array($category['id'], old('category', [])) ? 'selected' : '' }}>
                                {{ $category['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label for="image" class="block text-sm font-medium text-gray-700">عکس</label>
                @error('image')
                    <div class="text-red-500 text-sm mt-1" id="error-image">{{ $message }}</div>
                @enderror
                <input type="file" accept="image/jpg, image/jpeg" name="image" id="image" aria-describedby="error-image" class="mt-1 block w-full focus:outline-none transition-all border border-gray-300 rounded-md p-2 focus:ring focus:ring-blue-400">
            </div>
            <div>
                <label for="addOn" class="block text-sm font-medium text-gray-700">فایل ضمیمه</label>
                @error('addOn')
                    <div class="text-red-500 text-sm mt-1" id="error-addOn">{{ $message }}</div>
                @enderror
                <input type="file" accept="application/pdf,docx" name="addOn" id="addOn" aria-describedby="error-addOn" class="mt-1 block w-full focus:outline-none transition-all border border-gray-300 rounded-md p-2 focus:ring focus:ring-blue-400">
            </div>
            <div>
                <label for="body" class="block text-sm font-medium text-gray-700">متن خبر</label>
                @error('body')
                    <div class="text-red-500 text-sm mt-1" id="error-body">{{ $message }}</div>
                @enderror
                <textarea name="body" id="body" aria-describedby="error-body" class="mt-1  h-36 block w-full min-h-20 max-h-96 focus:outline-none transition-all border border-gray-300 rounded-md p-2 focus:ring focus:ring-blue-400">{{ old('body') }}</textarea>
            </div>
            <x-captcha/>
            <div>
                <button type="submit" class="w-full bg-blue-500 text-white font-semibold p-2 rounded-md hover:bg-blue-600 transition duration-200">ثبت</button>
            </div>
        </form>
    </main>
@endsection
