{{-- ===================== PRODUCT CARDS (Page 4) ===================== --}}
<section id="cards" class="relative w-full overflow-hidden bg-black"
    style="background-image: url('{{ asset('images/image 80.svg') }}'); background-size: cover; background-position: center; aspect-ratio: 1440 / 810;">

    <div class="relative w-full h-full" data-cards-toggle>

        {{-- Section Heading --}}
        <h2 class="absolute text-center text-white font-tagline leading-[0.9] tracking-tight w-full"
            style="top: 12%; font-size: 5vw;">
            How We Can<br>Support You
        </h2>

        {{-- Cards Stack Container --}}
        <div class="absolute left-1/2 -translate-x-1/2" style="top: 29%; width: 40%; height: 62%;">

            {{-- CARD 1 --}}
            <div class="card-1 absolute left-2z w-full cursor-pointer transition-all duration-700 ease-in-out overflow-hidden"
                style="height: 90%; border-radius: 1.4vw;" data-cards-toggle-button>

                {{-- Inactive Dark Overlay --}}
                <div
                    class="card-overlay absolute inset-0 bg-black/40 transition-opacity duration-700 pointer-events-none z-20">
                </div>

                {{-- Content Frame (Absolute Centered) --}}
                <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 flex flex-col items-center z-10 w-full"
                    style="gap: 5.15vw;">

                    {{-- Text Group --}}
                    <div class="w-full text-center" style="margin-top: 2vw;">
                        <h3 class="text-black leading-[1.1] font-tagline"
                            style="font-size: 2.5vw; margin-bottom: 1.2vw; letter-spacing: -0.02em;">
                            Location Scouting /<br>Finding The Right Spot
                        </h3>
                        <p class="text-black/80 font-tagline leading-tight mx-auto"
                            style="font-size: 1.05vw; max-width: 85%;">
                            Finding the right spot — because<br>location matters more than anything.
                        </p>
                    </div>

                    {{-- Huge Background Number --}}
                    <div class="text-[#CD3604] leading-none pointer-events-none select-none font-medium text-center font-tagline"
                        style="font-size: 8.9vw; letter-spacing: -0.05em;">
                        01
                    </div>
                </div>
            </div>

            {{-- CARD 2 --}}
            <div class="card-2 absolute left-0 w-full cursor-pointer transition-all duration-700 ease-in-out overflow-hidden shadow-[0_-10px_40px_rgba(0,0,0,0.2)]"
                style="height: 90%; border-radius: 1.4vw;" data-cards-toggle-button>

                {{-- Inactive Dark Overlay --}}
                <div
                    class="card-overlay absolute inset-0 bg-black/40 transition-opacity duration-700 pointer-events-none z-20">
                </div>

                {{-- Content Frame (Absolute Centered) --}}
                <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 flex flex-col items-center z-10 w-full"
                    style="gap: 5.15vw;">

                    {{-- Text Group --}}
                    <div class="w-full text-center">
                        <h3 class="text-black leading-[1.1] font-medium font-tagline"
                            style="font-size: 2.8vw; margin-bottom: 1.2vw; letter-spacing: -0.02em;">
                            Product Selection<br>+ Supplier Sourcing
                        </h3>
                        <p class="text-black/80 font-tagline leading-tight mx-auto font-tagline"
                            style="font-size: 1.05vw; max-width: 85%;">
                            We help you decide what to sell and<br>where to get it.
                        </p>
                    </div>

                    {{-- Huge Background Number --}}
                    <div class="text-[#CD3604] leading-none pointer-events-none select-none font-medium text-center font-tagline"
                        style="font-size: 8.9vw; letter-spacing: -0.05em;">
                        02
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<style>
    /* Base styles for both cards */
    .card-1,
    .card-2 {
        background-color: #FB480D;
    }

    /* DEFAULT STATE (Card 1 Dim, Card 2 Bright overlapping at bottom) */
    .card-1 {
        z-index: 10;
        top: 0;
        box-shadow: none;
    }

    .card-1 .card-overlay {
        opacity: 1;
        /* Dim by default */
    }

    .card-2 {
        z-index: 20;
        /* Overlaps Card 1, and sits larger than Card 1 while at rest */
        top: 74%;
        transform: scale(1.12);
        transform-origin: center;
        box-shadow: 0 -10px 40px rgba(0, 0, 0, 0.2);
    }

    .card-2 .card-overlay {
        opacity: 0;
        /* Bright by default */
    }

    /* STATE 1 (Card 1 Active: Comes forward) */
    [data-active-card="1"] .card-1 {
        z-index: 30;
        /* Comes forward over Card 2 */
        box-shadow: 0 20px 40px rgba(249, 22, 22, 0.3);
    }

    [data-active-card="1"] .card-1 .card-overlay {
        opacity: 0;
        /* Bright */
    }

    [data-active-card="1"] .card-2 {
        z-index: 20;
        top: 72%;
    }

    [data-active-card="1"] .card-2 .card-overlay {
        opacity: 1;
        /* Card 2 becomes dim */
    }

    /* STATE 2 (Card 2 Active: Bright and slides up to cover Card 1) */
    [data-active-card="2"] .card-1 {
        z-index: 10;
        top: 0;
    }

    [data-active-card="2"] .card-1 .card-overlay {
        opacity: 1;
        /* Card 1 is dim while covered */
    }

    [data-active-card="2"] .card-2 {
        z-index: 30;
        /* Comes forward */
        top: 0;
        /* Slides up */
        transform: scale(1);
        /* Shrinks back down to match Card 1's size */
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
    }

    [data-active-card="2"] .card-2 .card-overlay {
        opacity: 0;
        /* Bright */
    }
</style>