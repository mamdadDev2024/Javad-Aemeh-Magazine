@extends('layouts.default')

@section('title', 'مدیریت محتواها')

@section('body')
<section class="p-4 sm:p-6 rounded-lg shadow-md border border-slate-300 bg-white dark:bg-gray-800 dark:border-gray-700 m-2 sm:m-4">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-gray-100">لیست محتواها</h1>
    </div>

    {{-- 📚 نشریات --}}
    <div class="container mx-auto mb-8">
        <h3 class="font-semibold text-lg sm:text-xl mb-3 text-gray-700 dark:text-gray-200">نشریات</h3>
        @include('partials.content_table', [
            'items' => $magazines,
            'title' => 'نشریات',
            'editRoute' => 'writer.magazine.edit',
            'deleteRoute' => 'admin.magazine.delete',
            'showImage' => true,
            'showBody' => false,
            'showFiles' => false
        ])
    </div>

    {{-- 📅 رویدادها --}}
    <div class="container mx-auto mb-8">
        <h3 class="font-semibold text-lg sm:text-xl mb-3 text-gray-700 dark:text-gray-200">رویدادها</h3>
        @include('partials.content_table', [
            'items' => $events,
            'title' => 'رویدادها',
            'editRoute' => 'writer.event.edit',
            'deleteRoute' => 'admin.event.delete',
            'showImage' => true,
            'showBody' => true,
            'showFiles' => false
        ])
    </div>

    {{-- 📰 اخبار --}}
    <div class="container mx-auto mb-8">
        <h3 class="font-semibold text-lg sm:text-xl mb-3 text-gray-700 dark:text-gray-200">اخبار</h3>
        @include('partials.content_table', [
            'items' => $news,
            'title' => 'اخبار',
            'editRoute' => 'writer.new.edit',
            'deleteRoute' => 'admin.new.delete',
            'showImage' => true,
            'showBody' => true,
            'showFiles' => false
        ])
    </div>

    {{-- ✍️ مقالات ارسالی --}}
    <div class="container mx-auto mb-8">
        <h3 class="font-semibold text-lg sm:text-xl mb-3 text-gray-700 dark:text-gray-200">مقالات ارسالی</h3>
        @include('partials.content_table', [
            'items' => $recommends,
            'title' => 'مقالات ارسالی',
            'editRoute' => 'writer.recommend.edit',
            'deleteRoute' => 'admin.recommend.delete',
            'showImage' => false,
            'showBody' => false,
            'showFiles' => true
        ])
    </div>
</section>
@endsection
