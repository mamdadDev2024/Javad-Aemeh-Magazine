@props([
    'items',
    'type',
    'defaultLink',
    'containerClass' => ''
])

<div class="relative {{ $containerClass }} mb-4 w-full h-full overflow-hidden rounded-lg shadow-lg">
    <div class="swiper-wrapper">
        @foreach($items as $item)
            <a class="swiper-slide" href="{{ $item['slug'] ? route("$type.show", $item['slug']) : $defaultLink }}">
                <img src="{{ asset($item['image']) }}" alt="{{ $item['title'] }}"
                     class="w-full h-96 rounded-2xl object-cover transition-transform duration-300 transform @if($item['title'] === 'موسسه جواد الائمه') opacity-40 @endif" />
                <div class="absolute rounded-b-2xl bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black to-transparent">
                    <h4 class="text-lg text-white font-bold">{{ $item['title'] }}</h4>
                    <p class="text-sm text-white">{{ Illuminate\Support\Str::limit($item['body'] ?? '', 100) }}</p>
                </div>
            </a>
        @endforeach
    </div>

    {{-- کنترل‌های Swiper --}}
    <div class="swiper-pagination"></div>
    <div class="swiper-button-prev"></div>
    <div class="swiper-button-next"></div>
    <div class="swiper-scrollbar"></div>
</div>
