<section class="w-full overflow-hidden bg-white" data-features-section>

    {{-- ── Upper: text slide area (Matches 1440 × 624 proportions) ── --}}
    <div class="relative w-full mx-auto max-w-[1440px] h-[624px]">

        @if ($slides->isEmpty())
            {{-- Graceful fallback: an admin clearing every slide (or a DB hiccup)
            shouldn't leave the section blank or broken. --}}
            <div class="absolute inset-0 flex items-center justify-center px-6">
                <p class="font-tagline text-[#FB480D]/60 text-center text-2xl uppercase">
                    Benefit highlights are coming soon.
                </p>
            </div>
        @else
            {{-- Text content row — Figma "Frame 2147226596" --}}
            <div class="absolute flex items-start w-max transition-transform duration-500 ease-in-out left-[124.36px] top-[215.5px] gap-[353px]"
                data-features-track>

                {{-- Slide — Figma "Frame 2147226592" --}}
                @foreach ($slides as $slide)
                    <div class="flex items-start transition-opacity duration-500 {{ $loop->first ? 'opacity-100' : 'opacity-50' }} gap-[70px] h-[180px]"
                        data-feature-slide>
                        {{-- Title — Figma "Frame 2147226593" --}}
                        <div class="flex shrink-0 items-center justify-center w-[328.88px] h-[118px]">
                            @php
                                $titleWords = preg_split('/\s+/', trim($slide->title));
                                $titleLastWord = array_pop($titleWords);
                                $titleFirstLine = implode(' ', $titleWords);
                            @endphp
                            <h2
                                class="font-tagline max-w-[329px] break-words text-[#FB480D] capitalize text-[74px] leading-[80%]">
                                @if ($titleFirstLine !== '')
                                    {{ $titleFirstLine }}<br>-{{ $titleLastWord }}
                                @else
                                    {{ $titleLastWord }}
                                @endif
                            </h2>
                        </div>
                        <div class="shrink-0 text-[#FB480D] transition-opacity duration-500 {{ $loop->first ? '' : 'opacity-0 invisible' }} font-tagline w-[375.35px] text-[26px] leading-[30px] [&>p]:mt-[1.5em] [&>p:first-child]:mt-0"
                            data-features-body>
                            {!! \Illuminate\Support\Str::of($slide->body_text)->markdown() !!}
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Slider controls (Figma "Frame 2147226595") --}}
            <div class="absolute flex items-center gap-[32px] right-[48px] top-[566px]" data-features-controls>
                <span class="text-[#FB480D] font-tagline text-[22px] leading-none uppercase">
                    <span data-features-current>01</span> - <span
                        data-features-total>{{ str_pad($slides->count(), 2, '0', STR_PAD_LEFT) }}</span>
                </span>

                <div class="flex items-center gap-[18px]">
                    {{-- Previous arrow (40 % opacity) --}}
                    <button type="button" data-features-prev aria-label="Previous feature"
                        class="cursor-pointer transition-opacity hover:opacity-70">
                        <svg class="h-[15px] w-auto" viewBox="0 0 19.1 14.93" fill="none">
                            <path
                                d="M0.39 8.17C0 7.78 0 7.15 0.39 6.76L6.75 0.39C7.14 0 7.78 0 8.17 0.39C8.56 0.78 8.56 1.42 8.17 1.81L2.51 7.46L8.17 13.12C8.56 13.51 8.56 14.14 8.17 14.53C7.78 14.92 7.14 14.92 6.75 14.53L0.39 8.17ZM19.1 7.46V8.46H1.1V7.46V6.46H19.1V7.46Z"
                                fill="#FB480D" fill-opacity="0.4" />
                        </svg>
                    </button>

                    {{-- Next arrow (full opacity) --}}
                    <button type="button" data-features-next aria-label="Next feature"
                        class="cursor-pointer transition-opacity hover:opacity-70">
                        <svg class="h-[15px] w-auto" viewBox="0 0 19.1 14.93" fill="none">
                            <path
                                d="M18.7 8.17C19.09 7.78 19.09 7.15 18.7 6.76L12.34 0.39C11.95 0 11.32 0 10.92 0.39C10.53 0.78 10.53 1.42 10.92 1.81L16.58 7.46L10.92 13.12C10.53 13.51 10.53 14.14 10.92 14.53C11.32 14.92 11.95 14.92 12.34 14.53L18.7 8.17ZM0 7.46V8.46H18V7.46V6.46H0V7.46Z"
                                fill="#FB480D" />
                        </svg>
                    </button>
                </div>
            </div>
        @endif
    </div>

    {{-- ── Bottom: stats bar (Matches 1440 × 177 proportions) ── --}}
    <div class="relative w-full h-[177px] overflow-hidden bg-[#FB480D]">

        {{-- Background Image (Clean, No blur, No overlay) --}}
        <picture>
            <source srcset="{{ asset('images/image-69-2.webp') }}" type="image/webp">
            <img src="{{ asset('images/image-69-2.png') }}" alt="Stats background" loading="lazy" decoding="async"
                class="absolute inset-0 w-full h-full object-cover">
        </picture>

        {{-- Stats row --}}
        <div class="relative z-10 mx-auto w-[1153px] h-full flex items-center justify-between">

            {{-- Stat 1: 10+ | machines deployed --}}
            <div class="flex items-end gap-[16px] h-[73.32px]">
                <span
                    class="text-white uppercase font-tagline flex items-center justify-center w-[80px] h-[59px] text-[74px] leading-[80%]">10</span>

                <div class="shrink-0 bg-white w-[1px] h-[73.32px]"></div>

                <div class="flex flex-col justify-between items-start h-[73px] w-[175px]">
                    <svg class="shrink-0" width="12" height="12" viewBox="0 0 12 12" fill="none">
                        <path d="M6 0.5V11.5M0.5 6H11.5" stroke="white" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                    <span class="text-white font-tagline text-[20px] leading-[24px]">machines deployed</span>
                </div>
            </div>

            {{-- Stat 2: 1+ | year operating --}}
            <div class="flex items-end gap-[16px] h-[73.32px]">
                <span
                    class="text-white uppercase font-tagline flex items-center justify-center w-[33px] h-[59px] text-[74px] leading-[80%]">1</span>

                <div class="shrink-0 bg-white w-[1px] h-[73.32px]"></div>

                <div class="flex flex-col justify-between items-start h-[73px] w-[131px]">
                    <svg class="shrink-0" width="12" height="12" viewBox="0 0 12 12" fill="none">
                        <path d="M6 0.5V11.5M0.5 6H11.5" stroke="white" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                    <span class="text-white font-tagline text-[20px] leading-[24px]">year operating</span>
                </div>
            </div>

            {{-- Stat 3: 100% | in-house repairs --}}
            <div class="flex items-end gap-[16px] h-[73.32px]">
                <span
                    class="text-white uppercase font-tagline flex items-center justify-center w-[201px] h-[59px] text-[74px] leading-[80%]">100%</span>

                <div class="shrink-0 bg-white w-[1px] h-[73.32px]"></div>

                <span class="text-white font-tagline text-[20px] leading-[24px] pb-[1px]">in-house repairs</span>
            </div>
        </div>
    </div>
</section>