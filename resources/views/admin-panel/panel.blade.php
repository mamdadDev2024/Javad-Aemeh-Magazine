@extends('layouts.panel')
@section('title', 'پنل ادمین')
@section('body')
<main class="p-6 md:p-8">
    <form action="{{ route('admin.update.all') }}" id="panelForm" method="post" enctype="multipart/form-data"
          class="bg-gray-100 p-6 rounded-2xl shadow-md space-y-6 pb-16">
        @csrf
        <h2 class="text-2xl font-semibold text-center mb-6 text-blue-600">تغییر صفحه اصلی و ابزار ها</h2>

        {{-- دسته‌بندی‌ها --}}
        <section class=" rounded-xl p-3 shadow-xl my-3">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">دسته‌بندی‌ها</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-4">
                @foreach ($categories as $category)
                    <div class="bg-gray-200 rounded-xl p-4 text-center hover:bg-gray-300 transition duration-200">
                        {{ $category->name }}
                    </div>
                @endforeach
            </div>
            <label for="new_category" class="block mb-2 text-sm font-medium text-gray-700">دسته‌بندی جدید</label>
            <input type="text" name="new_category" id="new_category" value="{{ old('new_category') }}"
                   class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400 transition-all">
        </section>

        {{-- سطوح خبر --}}
        <section class=" rounded-xl p-3 shadow-xl my-3">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">سطوح خبر</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-4">
                @foreach ($scopes as $scope)
                    <div class="bg-gray-200 rounded-xl p-4 text-center hover:bg-gray-300 transition duration-200">
                        {{ $scope->name }}
                    </div>
                @endforeach
            </div>
            <label for="new_scope" class="block mb-2 text-sm font-medium text-gray-700">سطح جدید</label>
            <input type="text" name="new_scope" id="new_scope" value="{{ old('new_scope') }}"
                   class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400 transition-all">
        </section>

        {{-- اطلاعات بخش‌ها --}}
        <section class=" rounded-xl p-3 shadow-xl my-3">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">اطلاعات بخش‌ها</h3>
            @foreach ($sections as $section)
                <div class="mb-4 rounded-lg shadow-lg border p-2">
                    <label for="{{ $section->name }}" class="block mb-2 text-lg font-medium text-gray-700">
                        {{ __("fields.$section->name") }}
                    </label>
                    @if (in_array($section->name, ['defaultContentImage' , 'titleHeader', 'titleFooter']))

                        <input type="file" name="{{ $section->name }}" id="{{ $section->name }}" accept="image/*"
                               class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-400">
                    @else
                        <p class="text-sm font-medium text-gray-600 mb-2">متن فعلی: {{ $section->content }}</p>
                        <textarea name="{{ $section->name }}" id="{{ $section->name }}"
                                  class="w-full p-3 border rounded-md focus:outline-none focus:ring focus:ring-blue-400"
                                  placeholder="متن جدید را وارد کنید...">{{ old($section->name) }}</textarea>
                    @endif
                </div>
            @endforeach
            <p class=" text-lg font-medium mr-2">افزودن لینک مفید</p>
            <div class=" m-3 flex flex-col">
                <label for="name" class=" text-sm ">عنوان لینک</label>
                <input type="text" name="linkName" id="name"
                class=" p-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-400">
                @error("linkName")
                    <span class=" text-white bg-red-400 rounded-xl text-center">{{$message}}</span>
                @enderror
            </div>
            <div class=" m-3 flex flex-col">
                <label for="link" class=" text-sm ">لینک</label>
                <input type="text" name="link" id="link"
                class=" p-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-400">
                @error("link")
                    <span class=" text-white bg-red-400 rounded-xl text-center">{{$message}}</span>
                @enderror
            </div>
        </section>
    </form>
    <div class="flex flex-col gap-4 my-4">
        @if(!empty($links))
            <h3 class="font-bold text-lg text-center">لینک‌های مفید</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($links as $link)
                    <div class="flex flex-col items-start gap-2 rounded-xl p-4 shadow-xl border">
                        <!-- CHANGED: Add rel noopener for external links -->
                        <a href="{{$link['link']}}" rel="noopener" class="text-blue-500 font-semibold hover:underline">
                            {{$link['name']}}
                        </a>
                        <form method="POST" action="{{route("admin.delete.link")}}">
                            @csrf
                            <input type="hidden" value="{{$link['id']}}" name="id">
                            <button class="bg-red-500 px-3 py-2 rounded-xl text-white hover:bg-red-600 transition">
                                حذف
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
        <section class=" rounded-xl p-3 shadow-xl my-3">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">آمارها</h3>
            <div class="space-y-2">
                @foreach (['App\Models\Article', 'App\Models\Magazine', 'App\Models\Khabar', 'App\Models\Event', 'App\Models\View', 'App\Models\Comment'] as $stat)
                    <p class="text-gray-700">{{ __("stats.$stat") }}: {{ $stat::count() ?? 0 }}</p>
                @endforeach
                <p class="text-gray-700">{{ __('stats.App\Models\Like') }}: {{ DB::table('likes')->count() ?? 0 }}</p>
            </div>
        </section>
</main>
@endsection
