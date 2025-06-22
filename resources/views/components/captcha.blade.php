<div class="w-full max-w-full">
    {!! NoCaptcha::renderJs('fa') !!}
    {!! NoCaptcha::display() !!}
    @error("g-recaptcha-response")
        <span class="rounded-lg bg-red-500 px-2 py-1 my-1 text-white">
            {{$message}}
        </span>
    @enderror
</div>
