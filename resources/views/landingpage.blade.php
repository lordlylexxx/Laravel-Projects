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
            <a href="#properties" class="inline-flex items-center gap-2 rounded-lg border-2 border-brand-primary bg-transparent px-8 py-3.5 text-base font-semibold text-brand-dark transition-all hover:bg-brand-primary hover:text-white">
                <i class="fas fa-search"></i> Browse Tenant Portals
            </a>
        </div>
    </section>

    <!-- Tenant Carousel Section -->
    <section class="bg-white px-5 py-12 md:px-10 md:py-20" id="properties">
        <div class="mb-12 text-center opacity-0 animate-fade-in-up">
            <h2 class="mb-3 text-2xl font-bold text-brand-dark md:text-4xl">
                <i class="fas fa-store mr-2.5 text-brand-primary"></i>Featured Tenant Portals
            </h2>
            <p class="text-base text-brand-medium">Explore active accommodation providers in Impasugong</p>
        </div>

        <div class="relative mx-auto max-w-[1400px] overflow-hidden">
            <div class="flex transition-transform duration-500 ease-in-out" id="carouselTrack">
                @forelse(($featuredTenants ?? collect()) as $tenant)
                    @php
                        $settings = $tenant->landingSettings();
                        $tenantLogo = $tenant->getLogoUrl() ?: ($settings['hero_image_url'] ?? '/SYSTEMLOGO.png');
                        $plan = (string) ($tenant->plan ?? '');
                        $planName = match (true) {
                            str_starts_with($plan, 'custom:') => 'Custom',
                            $plan === 'pro' => 'Premium',
                            $plan === 'plus' => 'Standard',
                            $plan === 'basic' => 'Basic',
                            default => 'Tenant',
                        };
                    @endphp
                    <div class="carousel-slide flex w-[312px] shrink-0 justify-center sm:w-[328px]">
                        <div class="flex h-[520px] w-[288px] flex-col overflow-hidden rounded-[20px] border border-brand-soft bg-white shadow-[0_8px_30px_rgba(27,94,32,0.12)] transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_16px_40px_rgba(27,94,32,0.18)] sm:w-[300px]">
                            <div class="flex h-[200px] w-full shrink-0 items-center justify-center overflow-hidden bg-gradient-to-b from-slate-50 to-white p-2">
                                <img src="{{ $tenantLogo }}" alt="{{ $tenant->name }}" class="h-full w-full object-contain object-center">
                            </div>
                            <div class="flex min-h-0 flex-1 flex-col px-4 pb-4 pt-3">
                                <span class="mb-2.5 inline-flex w-fit shrink-0 items-center gap-1.5 rounded-full bg-brand-soft px-3 py-1 text-xs font-semibold text-brand-dark">
                                    <i class="fas fa-store text-[0.7rem]"></i> {{ $planName }} Portal
                                </span>
                                <h3 class="line-clamp-2 min-h-[2.75rem] text-base font-bold leading-snug text-brand-dark">{{ $tenant->name }}</h3>
                                <div class="mt-1 flex min-h-[1.375rem] items-start gap-1.5 text-xs text-brand-medium" title="{{ $tenant->domain ?: 'localhost' }}">
                                    <i class="fas fa-globe mt-0.5 shrink-0 text-[0.7rem]"></i>
                                    <span class="line-clamp-1 break-all">{{ $tenant->domain ?: 'localhost' }}</span>
                                </div>
                                <div class="mt-3 flex shrink-0 flex-col gap-2 border-b border-brand-soft pb-3 text-xs text-brand-dark">
                                    <span class="flex items-center gap-1.5"><i class="fas fa-calendar-check w-4 shrink-0 text-brand-primary"></i> Booking Enabled</span>
                                    <span class="flex items-center gap-1.5"><i class="fas fa-message w-4 shrink-0 text-brand-primary"></i> Messaging {{ $tenant->feature_messaging ? 'On' : 'Off' }}</span>
                                    <span class="flex items-center gap-1.5"><i class="fas fa-user w-4 shrink-0 text-brand-primary"></i> Owner: {{ $tenant->owner?->name ?? 'N/A' }}</span>
                                </div>
                                <div class="mt-auto flex shrink-0 items-end justify-between gap-2 border-t border-transparent pt-3">
                                    <div class="min-w-0 text-sm font-bold leading-tight text-brand-primary">
                                        Visit Portal
                                        <span class="block text-xs font-normal text-brand-medium">live app</span>
                                    </div>
                                    <a href="{{ $tenant->publicUrl() }}" class="inline-flex shrink-0 items-center rounded-lg border-2 border-brand-primary bg-transparent px-3 py-2 text-xs font-semibold text-brand-dark transition-colors hover:bg-brand-primary hover:text-white sm:text-sm">
                                        Open
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="carousel-slide m-0 min-w-full px-3">
                        <div class="rounded-[20px] border border-brand-soft bg-white p-8 text-center shadow-[0_8px_30px_rgba(27,94,32,0.12)]">
                            <h3 class="mb-3 text-lg font-bold text-brand-dark">No Tenant Portals Yet</h3>
                            <p class="text-brand-medium">Tenant showcases will appear here as owners complete onboarding.</p>
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="mt-10 flex justify-center gap-3">
                <button type="button" class="flex h-12 w-12 items-center justify-center rounded-full border-0 bg-brand-soft text-xl text-brand-dark transition-all hover:scale-110 hover:bg-brand-primary hover:text-white" id="prevBtn" aria-label="Previous">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button type="button" class="flex h-12 w-12 items-center justify-center rounded-full border-0 bg-brand-soft text-xl text-brand-dark transition-all hover:scale-110 hover:bg-brand-primary hover:text-white" id="nextBtn" aria-label="Next">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section class="px-5 py-16 md:px-10 md:py-24" id="pricing">
        <div class="mx-auto mb-11 max-w-[760px] text-center text-brand-dark opacity-0 animate-fade-in-up">
            <h2 class="mb-3 text-2xl font-bold md:text-4xl">
                <i class="fas fa-tags mr-2.5"></i>Pricing Plans for Property Owners
            </h2>
            <p class="text-base leading-relaxed">Choose a plan that fits your rental business and unlock the tools you need to grow on ImpaStay.</p>
        </div>

        <div class="mx-auto grid max-w-[1200px] grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
            <div class="rounded-[20px] border border-white/35 bg-white/15 p-8 backdrop-blur-sm transition-all duration-300 hover:-translate-y-2.5 hover:shadow-2xl opacity-0 animate-fade-in-up-d1">
                <h3 class="mb-2.5 text-xl font-bold text-brand-dark">Basic Plan</h3>
                <div class="mb-5 text-3xl font-extrabold text-brand-dark">₱299</div>
                <ul class="space-y-3 text-brand-dark">
                    <li class="flex items-start gap-2.5 text-[0.95rem] leading-snug"><i class="fa-solid fa-check mt-0.5 text-brand-dark"></i> 3 property listings</li>
                    <li class="flex items-start gap-2.5 text-[0.95rem] leading-snug"><i class="fa-solid fa-check mt-0.5 text-brand-dark"></i> Basic reporting</li>
                    <li class="flex items-start gap-2.5 text-[0.95rem] leading-snug"><i class="fa-solid fa-check mt-0.5 text-brand-dark"></i> Booking management</li>
                </ul>
                <a href="{{ route('register', ['role' => 'owner', 'plan' => 'basic']) }}" class="mt-6 inline-flex w-full items-center justify-center rounded-xl border border-brand-primary bg-white/75 px-3.5 py-3 font-bold text-brand-primary transition-all hover:-translate-y-0.5 hover:bg-brand-primary hover:text-white">
                    Register for Basic
                </a>
            </div>

            <div class="rounded-[20px] border-2 border-brand-primary bg-white/15 p-8 backdrop-blur-sm transition-all duration-300 hover:-translate-y-2.5 hover:shadow-2xl max-md:scale-100 md:scale-[1.03] opacity-0 animate-fade-in-up-d2">
                <h3 class="mb-2.5 text-xl font-bold text-brand-dark">Standard Plan</h3>
                <div class="mb-5 text-3xl font-extrabold text-brand-dark">₱499</div>
                <ul class="space-y-3 text-brand-dark">
                    <li class="flex items-start gap-2.5 text-[0.95rem] leading-snug"><i class="fa-solid fa-check mt-0.5 text-brand-dark"></i> Up to 10 listings</li>
                    <li class="flex items-start gap-2.5 text-[0.95rem] leading-snug"><i class="fa-solid fa-check mt-0.5 text-brand-dark"></i> Advanced reporting</li>
                    <li class="flex items-start gap-2.5 text-[0.95rem] leading-snug"><i class="fa-solid fa-check mt-0.5 text-brand-dark"></i> Analytics dashboard</li>
                </ul>
                <a href="{{ route('register', ['role' => 'owner', 'plan' => 'plus']) }}" class="mt-6 inline-flex w-full items-center justify-center rounded-xl border border-brand-primary bg-white/75 px-3.5 py-3 font-bold text-brand-primary transition-all hover:-translate-y-0.5 hover:bg-brand-primary hover:text-white">
                    Register for Standard
                </a>
            </div>

            <div class="rounded-[20px] border border-white/35 bg-white/15 p-8 backdrop-blur-sm transition-all duration-300 hover:-translate-y-2.5 hover:shadow-2xl md:col-span-2 lg:col-span-1 opacity-0 animate-fade-in-up-d3">
                <h3 class="mb-2.5 text-xl font-bold text-brand-dark">Premium Plan</h3>
                <div class="mb-5 text-3xl font-extrabold text-brand-dark">₱799</div>
                <ul class="space-y-3 text-brand-dark">
                    <li class="flex items-start gap-2.5 text-[0.95rem] leading-snug"><i class="fa-solid fa-check mt-0.5 text-brand-dark"></i> Unlimited listings</li>
                    <li class="flex items-start gap-2.5 text-[0.95rem] leading-snug"><i class="fa-solid fa-check mt-0.5 text-brand-dark"></i> Priority support</li>
                    <li class="flex items-start gap-2.5 text-[0.95rem] leading-snug"><i class="fa-solid fa-check mt-0.5 text-brand-dark"></i> Featured listing promotion</li>
                    <li class="flex items-start gap-2.5 text-[0.95rem] leading-snug"><i class="fa-solid fa-check mt-0.5 text-brand-dark"></i> Advanced analytics</li>
                </ul>
                <a href="{{ route('register', ['role' => 'owner', 'plan' => 'pro']) }}" class="mt-6 inline-flex w-full items-center justify-center rounded-xl border border-brand-primary bg-white/75 px-3.5 py-3 font-bold text-brand-primary transition-all hover:-translate-y-0.5 hover:bg-brand-primary hover:text-white">
                    Register for Premium
                </a>
            </div>
        </div>
    </section>

    @include('partials.central-public-footer')

    <script>
        const carouselTrack = document.getElementById('carouselTrack');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');

        let currentIndex = 0;
        const slides = document.querySelectorAll('.carousel-slide');
        const totalSlides = slides.length;

        function slideWidthPx() {
            return window.innerWidth < 640 ? 312 : 328;
        }

        function visibleSlides() {
            return window.innerWidth < 768 ? 1 : 3;
        }

        function updateCarousel() {
            if (!carouselTrack) return;
            const v = visibleSlides();
            const maxIndex = Math.max(0, totalSlides - v);
            currentIndex = Math.min(currentIndex, maxIndex);
            const offset = -currentIndex * slideWidthPx();
            carouselTrack.style.transform = `translateX(${offset}px)`;
        }

        prevBtn?.addEventListener('click', function () {
            if (currentIndex > 0) {
                currentIndex--;
                updateCarousel();
            }
        });

        nextBtn?.addEventListener('click', function () {
            const maxIndex = Math.max(0, totalSlides - visibleSlides());
            if (currentIndex < maxIndex) {
                currentIndex++;
                updateCarousel();
            }
        });

        window.addEventListener('resize', updateCarousel);

        setInterval(function () {
            const maxIndex = Math.max(0, totalSlides - visibleSlides());
            if (currentIndex < maxIndex) {
                currentIndex++;
            } else {
                currentIndex = 0;
            }
            updateCarousel();
        }, 5000);

        updateCarousel();
    </script>
</body>
</html>
