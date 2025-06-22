@extends('layouts.default')
@section('title', 'محتواها')
@section('body')
<section class="p-4 overflow-x-auto sm:p-6 rounded-lg shadow-md border border-slate-300 bg-white m-2 sm:m-4">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-xl sm:text-2xl font-bold">لیست محتواها</h1>
    </div>
    @if ($Magazines->isNotEmpty())
        <div class="container mx-auto mb-8">
            <h3 class="font-semibold text-lg sm:text-xl mb-3">نشریات</h3>
            @include('partials.magazine_table', ['items' => $Magazines, 'type' => 'نشریه'])
        </div>
    @else
        <div class="bg-yellow-100 text-yellow-800 text-center py-4 rounded-md">
            نشریات موجود نیست
        </div>
    @endif
    @if ($Events->isNotEmpty())
        <div class="container mx-auto mb-8">
            <h3 class="font-semibold text-lg sm:text-xl mb-3">رویدادها</h3>
            @include('partials.event_table', ['items' => $Events, 'type' => 'رویداد'])
        </div>
    @else
        <div class="bg-yellow-100 text-yellow-800 text-center py-4 rounded-md">
            رویدادها موجود نیست
        </div>
    @endif
    @if ($News->isNotEmpty())
        <div class="container mx-auto mb-8">
            <h3 class="font-semibold text-lg sm:text-xl mb-3">اخبار</h3>
            @include('partials.news_table', ['items' => $News, 'type' => 'خبر'])
        </div>
    @else
        <div class="bg-yellow-100 text-yellow-800 text-center py-4 rounded-md">
            اخبار موجود نیست
        </div>
    @endif
    @if ($Recommends->isNotEmpty())
        <div class="container mx-auto mb-8">
            <h3 class="font-semibold text-lg sm:text-xl mb-3">مقاله ارسالی</h3>
            @include('partials.recommend_table', ['items' => $Recommends, 'type' => 'پیشنهادات'])
        </div>
    @else
        <div class="bg-yellow-100 text-yellow-800 text-center py-4 rounded-md">
            مقاله ی ارسالی موجود نیست
        </div>
    @endif
</section>
@endsection
