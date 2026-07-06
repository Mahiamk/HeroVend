{{-- ===================== HERO ===================== --}}
<section class="relative min-h-screen w-full overflow-hidden bg-[#1E1E1E]">
    <picture>
        <source srcset="{{ asset('images/image-69.webp') }}" type="image/webp">
        <img src="{{ asset('images/image-69.png') }}" alt="" fetchpriority="high" decoding="async"
            class="absolute inset-0 h-full w-full object-cover">
    </picture>

    {{-- Mobile / portrait: flat orange tint (the landscape V overlay can't fit a
    portrait screen without cropping the mark, so it's swapped for a clean tint). --}}
    <div class="absolute inset-0 bg-[rgba(251,72,13,0.85)] lg:hidden"></div>

    {{-- Desktop / landscape: orange tint + "V" cut-out overlay, scaled to cover. --}}
    <img src="{{ asset('images/subtract.svg') }}" alt=""
        class="absolute inset-0 hidden h-full w-full object-cover lg:block">

    <div class="relative z-10 flex min-h-screen flex-col justify-between p-6 sm:p-10 lg:p-14">
        <header>
            <img src="{{ asset('images/frame-2147226374.svg') }}" alt="HeroVend" class="h-7 w-auto sm:h-8">
        </header>

        <footer class="flex w-full flex-col items-start justify-between gap-6 sm:flex-row sm:items-end">
            <p class="font-tagline text-3xl text-white">
                We Sell Vending Machines.<br>
                And Everything It Takes To Run Them.
            </p>

            <p class="font-tagline shrink-0 text-lg text-white [text-shadow:0px_4px_43.4px_#000000] sm:text-[22px]">
                Scroll Down
            </p>
        </footer>
    </div>
</section>