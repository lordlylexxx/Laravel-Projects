<!DOCTYPE html>
<html lang="en">
<head>
    @include('partials.central-public-head', ['pageTitle' => 'IMPASUGONG TOURISM | Impasugong Accommodations'])
</head>
<body
    class="min-h-screen font-sans text-brand-dark antialiased bg-cover bg-center bg-fixed"
    style="background-image: linear-gradient(135deg, rgba(255,255,255,0.95) 0%, rgba(255,255,255,0.85) 50%, rgba(27, 94, 32, 0.1) 100%), url('/COMMUNAL.jpg');"
>
    @include('partials.central-public-nav', ['active' => 'landing'])

    <!-- Hero Section -->
    <section class="flex min-h-screen flex-col items-center justify-center bg-gradient-to-br from-[rgba(27,94,32,0.08)] to-[rgba(46,125,50,0.05)] px-5 pb-20 pt-24 text-center md:px-10 md:pt-28">
        <div class="mb-5 flex flex-wrap items-center justify-center gap-3.5 opacity-0 animate-fade-in-up-d1">
            <img src="/Love%20Impasugong.png" alt="Love Impasugong Logo" class="h-[102px] w-[102px] object-contain md:h-[200px] md:w-[200px]">
            <img src="/SYSTEMLOGO.png" alt="System Logo" class="h-[102px] w-[102px] object-contain md:h-[200px] md:w-[200px]">
            <img src="/Lgu%20Socmed%20Template-02.png" alt="LGU Impasugong" class="h-[102px] w-[102px] object-contain md:h-[200px] md:w-[200px]">
        </div>

        <h1 class="mb-5 text-3xl font-extrabold tracking-tight text-brand-dark opacity-0 animate-fade-in-up-d1 md:text-5xl lg:text-[3.5rem]">
            Find Your Perfect <span class="text-brand-primary">Stay</span>
        </h1>

        <div class="mb-8 inline-flex items-center gap-2.5 rounded-full border-2 border-brand-soft bg-white px-7 py-3 text-sm font-semibold text-brand-dark shadow-[0_4px_15px_rgba(27,94,32,0.1)] opacity-0 animate-fade-in-up-d1">
            <i class="fas fa-home text-brand-primary"></i>
            <span>Your Gateway to Impasugong Accommodations</span>
        </div>

        <p class="mb-10 max-w-[700px] text-base leading-relaxed text-brand-medium opacity-0 animate-fade-in-up-d2 md:text-xl">
            Discover traveller-inns, Airbnb stays, and daily rentals.
            Book unique accommodations and experience local hospitality.
        </p>

        <div class="flex flex-col items-center justify-center gap-4 opacity-0 animate-fade-in-up-d2 sm:flex-row">
            <a href="/register" class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-br from-brand-dark to-brand-primary px-8 py-3.5 text-base font-semibold text-white shadow-[0_4px_15px_rgba(46,125,50,0.3)] transition-all hover:-translate-y-0.5 hover:shadow-[0_6px_20px_rgba(46,125,50,0.4)]">
                <i class="fas fa-rocket"></i> Get Started
            </a>
            <a href="#pricing" class="inline-flex items-center gap-2 rounded-lg border-2 border-brand-primary bg-transparent px-8 py-3.5 text-base font-semibold text-brand-dark transition-all hover:bg-brand-primary hover:text-white">
                <i class="fas fa-tags"></i> View pricing plans
            </a>
        </div>
    </section>

    @php
        $plans = ($landingPlans ?? collect())->values();
        $planCount = $plans->count();
        $useCarousel = $planCount > 3;
        $anim = ['opacity-0 animate-fade-in-up-d1', 'opacity-0 animate-fade-in-up-d2', 'opacity-0 animate-fade-in-up-d3'];
    @endphp
    <!-- Pricing Section -->
    <section class="px-5 py-16 md:px-10 md:py-24" id="pricing">
        <div class="mx-auto mb-11 max-w-[760px] text-center text-brand-dark opacity-0 animate-fade-in-up">
            <h2 class="mb-3 text-2xl font-bold md:text-4xl">
                <i class="fas fa-tags mr-2.5 text-brand-primary"></i>Pricing Plans for Property Owners
            </h2>
            <p class="text-base leading-relaxed">Choose a plan that fits your rental business and unlock the tools you need to grow on ImpaStay.</p>
        </div>

        @forelse($plans as $plan)
            @if($loop->first)
                @if($useCarousel)
                    <div class="relative mx-auto max-w-[1400px] opacity-0 animate-fade-in-up-d2">
                        <div
                            id="planPricingTrack"
                            class="flex items-stretch gap-6 overflow-x-auto scroll-smooth pb-2 pl-1 pr-1 [scrollbar-width:thin] snap-x snap-mandatory"
                            style="-webkit-overflow-scrolling: touch;"
                        >
                @else
                    <div class="mx-auto grid max-w-[1200px] grid-cols-1 items-stretch gap-8 md:grid-cols-2 lg:grid-cols-3">
                @endif
            @endif

            @if($useCarousel)
                <div class="plan-pricing-slide flex h-full min-h-0 shrink-0 snap-start w-[min(100%,300px)] flex-col sm:w-[308px] {{ $anim[$loop->index] ?? 'opacity-0 animate-fade-in-up' }}">
                    @include('partials.central-landing-plan-card', ['plan' => $plan])
                </div>
            @else
                <div class="flex h-full min-h-0 flex-col {{ $anim[$loop->index] ?? 'opacity-0 animate-fade-in-up' }}">
                    @include('partials.central-landing-plan-card', ['plan' => $plan])
                </div>
            @endif

            @if($loop->last)
                        </div>
                @if($useCarousel)
                        <div class="mt-8 flex justify-center gap-3">
                            <button type="button" class="flex h-12 w-12 items-center justify-center rounded-full border-0 bg-brand-soft text-xl text-brand-dark transition-all hover:scale-110 hover:bg-brand-primary hover:text-white" id="planPricingPrev" aria-label="Previous plans">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button type="button" class="flex h-12 w-12 items-center justify-center rounded-full border-0 bg-brand-soft text-xl text-brand-dark transition-all hover:scale-110 hover:bg-brand-primary hover:text-white" id="planPricingNext" aria-label="Next plans">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                @endif
            @endif
        @empty
            <div class="mx-auto max-w-[760px] rounded-[20px] border border-brand-soft bg-white/40 px-6 py-10 text-center text-brand-medium backdrop-blur-sm opacity-0 animate-fade-in-up">
                <p class="text-lg font-semibold text-brand-dark">Plans coming soon</p>
                <p class="mt-2 text-sm">Owner pricing will appear here once configured.</p>
            </div>
        @endforelse

        @if($useCarousel)
            <script>
                (function () {
                    var track = document.getElementById('planPricingTrack');
                    var prev = document.getElementById('planPricingPrev');
                    var next = document.getElementById('planPricingNext');
                    if (!track || !prev || !next) return;
                    function step() {
                        var slide = track.querySelector('.plan-pricing-slide');
                        var w = slide ? slide.getBoundingClientRect().width : 300;
                        return Math.min(360, w + 24);
                    }
                    prev.addEventListener('click', function () {
                        track.scrollBy({ left: -step(), behavior: 'smooth' });
                    });
                    next.addEventListener('click', function () {
                        track.scrollBy({ left: step(), behavior: 'smooth' });
                    });
                })();
            </script>
        @endif
    </section>

    @include('partials.central-public-footer')
</body>
</html>
