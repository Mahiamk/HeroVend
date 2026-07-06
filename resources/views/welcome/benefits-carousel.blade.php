{{-- ===================== BENEFITS CAROUSEL (Page 3) ===================== --}}
<section class="w-full overflow-hidden bg-[#1A1A1A] text-white py-20 px-6 sm:px-10 lg:px-14">
    <div class="max-w-7xl mx-auto" x-data="{ activeSlide: 0, totalSlides: {{ $slides->count() }} }">

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

            {{-- Left Side: Vending Machine Product Render View --}}
            <div
                class="relative flex justify-center items-center overflow-hidden rounded-2xl bg-gradient-to-b from-zinc-800 to-zinc-900 border border-zinc-800 p-8 min-h-[400px] lg:min-h-[600px]">
                {{-- Static Machine Background Visual Indicator --}}
                <img src="{{ asset('images/the-champion.png') }}" alt="HeroVend The Champion"
                    class="h-auto max-h-[500px] w-auto object-contain drop-shadow-[0_25px_50px_rgba(0,0,0,0.5)] z-10">

                {{-- Subtle abstract dynamic background flare matching current active slide index --}}
                <div class="absolute inset-0 bg-[#FB480D]/10 opacity-40 blur-[120px] rounded-full transform -translate-x-12 transition-all duration-700"
                    :style="'transform: scale(' + (1 + (activeSlide * 0.15)) + ') rotate(' + (activeSlide * 45) + 'deg)'">
                </div>
            </div>

            {{-- Right Side: Dynamic Content Carousel Panel --}}
            <div class="flex flex-col justify-between min-h-[350px] lg:min-h-[450px]">

                <div class="relative flex-1">
                    @foreach($slides as $index => $slide)
                        <div x-show="activeSlide === {{ $index }}"
                            x-transition:enter="transition ease-out duration-500 delay-100"
                            x-transition:enter-start="opacity-0 translate-x-8"
                            x-transition:enter-end="opacity-100 translate-x-0"
                            x-transition:leave="transition ease-in duration-300 absolute inset-0"
                            x-transition:leave-start="opacity-100 translate-x-0"
                            x-transition:leave-end="opacity-0 -translate-x-8" class="space-y-6"
                            style="display: {{ $index === 0 ? 'block' : 'none' }};">

                            {{-- Header Track ID Indicator --}}
                            <div class="text-[#FB480D] font-mono tracking-widest text-sm uppercase">
                                Capability // 0{{ $index + 1 }}
                            </div>

                            {{-- Dynamic Title --}}
                            <h3 class="font-tagline text-white leading-tight tracking-tight font-bold"
                                style="font-size: clamp(2.5rem, 4vw, 60px);">
                                {{ $slide->title }}
                            </h3>

                            {{-- Dynamic Body Text --}}
                            <p class="font-tagline text-zinc-400 leading-relaxed font-light max-w-xl"
                                style="font-size: clamp(1.1rem, 1.3vw, 20px);">
                                {{ $slide->body_text }}
                            </p>
                        </div>
                    @endforeach
                </div>

                {{-- Interactive Progress Pagination Triggers --}}
                <div class="flex items-center justify-between border-t border-zinc-800 pt-8 mt-6">

                    {{-- Pagination Fraction Track Counter --}}
                    <div class="font-tagline text-zinc-500 font-medium">
                        <span class="text-[#FB480D]" x-text="String(activeSlide + 1).padStart(2, '0')">01</span>
                        <span class="mx-1">/</span>
                        <span x-text="String(totalSlides).padStart(2, '0')">05</span>
                    </div>

                    {{-- Navigation Interactive Sliders --}}
                    <div class="flex items-center gap-4">
                        {{-- Prev Control Action Button --}}
                        <button type="button"
                            @click="activeSlide = (activeSlide === 0) ? totalSlides - 1 : activeSlide - 1"
                            class="flex items-center justify-center border border-zinc-800 hover:border-zinc-700 bg-zinc-900 hover:bg-zinc-800 text-white rounded-full transition h-12 w-12 cursor-pointer group"
                            aria-label="Previous Slide">
                            <svg class="h-4 w-4 transform group-hover:-translate-x-0.5 transition-transform"
                                viewBox="0 0 19.1 14.93" fill="none">
                                <path
                                    d="M0.39 8.17C0 7.78 0 7.15 0.39 6.76L6.75 0.39C7.14 0 7.78 0 8.17 0.39C8.56 0.78 8.56 1.42 8.17 1.81L2.51 7.46L8.17 13.12C8.56 13.51 8.56 14.14 8.17 14.53C7.78 14.92 7.14 14.92 6.75 14.53L0.39 8.17ZM19.1 7.46V8.46H1.1V7.46V6.46H19.1V7.46Z"
                                    fill="currentColor" />
                            </svg>
                        </button>

                        {{-- Next Control Action Button --}}
                        <button type="button"
                            @click="activeSlide = (activeSlide === totalSlides - 1) ? 0 : activeSlide + 1"
                            class="flex items-center justify-center bg-[#FB480D] hover:bg-[#ff5a24] text-white rounded-full transition h-12 w-12 cursor-pointer group"
                            aria-label="Next Slide">
                            <svg class="h-4 w-4 transform group-hover:translate-x-0.5 transition-transform"
                                viewBox="0 0 19.1 14.93" fill="none">
                                <path
                                    d="M18.7 8.17C19.09 7.78 19.09 7.15 18.7 6.76L12.34 0.39C11.95 0 11.32 0 10.92 0.39C10.53 0.78 10.53 1.42 10.92 1.81L16.58 7.46L10.92 13.12C10.53 13.51 10.53 14.14 10.92 14.53C11.32 14.92 11.95 14.92 12.34 14.53L18.7 8.17ZM0 7.46V8.46H18V7.46V6.46H0V7.46Z"
                                    fill="currentColor" />
                            </svg>
                        </button>
                    </div>

                </div>

            </div>
        </div>

    </div>
</section>