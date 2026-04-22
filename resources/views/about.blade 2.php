<!DOCTYPE html>
<html lang="en">
<head>
    @include('partials.central-public-head', ['pageTitle' => 'About Us | IMPASUGONG TOURISM'])
</head>
<body
    class="min-h-screen font-sans text-brand-dark antialiased bg-cover bg-center bg-fixed"
    style="background-image: linear-gradient(135deg, rgba(255,255,255,0.95) 0%, rgba(255,255,255,0.85) 50%, rgba(27, 94, 32, 0.1) 100%), url('/COMMUNAL.jpg');"
>
    @include('partials.central-public-nav', ['active' => 'about'])

    <main class="mx-auto max-w-6xl px-5 pb-20 pt-24 md:px-10 md:pt-28">
        <header class="mx-auto mb-10 max-w-3xl text-center">
            <div class="mb-8 flex flex-wrap items-center justify-center gap-3.5">
                <img src="/Love%20Impasugong.png" alt="Love Impasugong" class="h-20 w-20 object-contain md:h-[120px] md:w-[120px]">
                <img src="/SYSTEMLOGO.png" alt="IMPASUGONG TOURISM" class="h-20 w-20 object-contain md:h-[120px] md:w-[120px]">
                <img src="/Lgu%20Socmed%20Template-02.png" alt="LGU Impasugong, Bukidnon" class="h-20 w-20 object-contain md:h-[120px] md:w-[120px]">
            </div>
            <h1 class="mb-4 text-3xl font-extrabold tracking-tight text-brand-dark md:text-5xl">
                About <span class="text-brand-primary">IMPASUGONG TOURISM</span>
            </h1>
            <p class="text-lg leading-relaxed text-brand-medium">
                IMPASUGONG TOURISM connects guests with trusted accommodations across Impasugong—supporting local tourism,
                property owners, and the community through one central platform.
            </p>
            <a href="{{ route('landing') }}" class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-brand-primary hover:underline">
                <i class="fas fa-arrow-left"></i> Back to home
            </a>
        </header>

        @php
            // Use public/team-photos/ — NOT public/about/ (that folder shadows the /about route on php artisan serve).
            $resolveAboutImage = function (string $stem): ?string {
                $stemsToTry = match ($stem) {
                    'ux-ui-designer' => ['ux-ui-designer', 'ui-ux-designer'],
                    'documentor' => ['documentor', 'ui-ux-documenter'],
                    default => [$stem],
                };
                foreach ($stemsToTry as $tryStem) {
                    foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
                        $rel = 'team-photos/' . $tryStem . '.' . $ext;
                        if (is_file(public_path($rel))) {
                            return asset($rel);
                        }
                    }
                }

                return null;
            };

            $mayor = ['stem' => 'mayor', 'name' => null, 'role' => 'Mayor', 'bio' => 'Leads the municipality’s vision for inclusive growth and tourism.'];
            $tourismHead = ['stem' => 'tourism-head', 'name' => null, 'role' => 'Municipal Tourism Officer (Impasugong)', 'bio' => 'Champions destinations, visitor experience, and local hospitality.'];
            // 3 programmers, 1 UX/UI designer, 1 documentor (5 people total).
            $developmentTeam = [
                ['stem' => 'programmer-1', 'name' => null, 'role' => 'Programmer', 'bio' => 'Builds and maintains ImpaStay’s platform, features, and integrations.'],
                ['stem' => 'programmer-2', 'name' => null, 'role' => 'Programmer', 'bio' => 'Builds and maintains ImpaStay’s platform, features, and integrations.'],
                ['stem' => 'programmer-3', 'name' => null, 'role' => 'Programmer', 'bio' => 'Builds and maintains ImpaStay’s platform, features, and integrations.'],
                ['stem' => 'ux-ui-designer', 'name' => null, 'role' => 'UX/UI Designer', 'bio' => 'Designs interfaces and user experiences for guests and property owners.'],
                ['stem' => 'documentor', 'name' => null, 'role' => 'Documentor', 'bio' => 'Creates documentation, UX/UI notes, and user-facing guides for the platform.'],
            ];
        @endphp

        <section class="mb-10 md:mb-12" aria-labelledby="about-leadership">
            <h2 id="about-leadership" class="mb-5 flex items-center justify-center gap-2 text-center text-xl font-bold text-brand-dark md:text-2xl">
                <i class="fas fa-landmark text-brand-primary"></i> Leadership
            </h2>
            <div class="mx-auto flex max-w-6xl flex-col items-center gap-6">
                <div class="w-full max-w-xs sm:max-w-sm">
                    @include('partials.about-team-member', [
                        'stem' => $mayor['stem'],
                        'name' => $mayor['name'],
                        'role' => $mayor['role'],
                        'bio' => $mayor['bio'],
                        'imageUrl' => $resolveAboutImage($mayor['stem']),
                    ])
                </div>
                <div class="w-full max-w-xs sm:max-w-sm">
                    @include('partials.about-team-member', [
                        'stem' => $tourismHead['stem'],
                        'name' => $tourismHead['name'],
                        'role' => $tourismHead['role'],
                        'bio' => $tourismHead['bio'],
                        'imageUrl' => $resolveAboutImage($tourismHead['stem']),
                    ])
                </div>
            </div>
        </section>

        <section class="mb-10 md:mb-12" aria-labelledby="about-development">
            <h2 id="about-development" class="mb-5 flex items-center justify-center gap-2 text-center text-xl font-bold text-brand-dark md:text-2xl">
                <i class="fas fa-users text-brand-primary"></i> Development team
            </h2>
            <p class="mx-auto mb-6 max-w-2xl text-center text-sm text-brand-medium">
                Three programmers, one UX/UI designer, and one documentor.
            </p>
            <div class="mx-auto flex max-w-6xl flex-wrap justify-center gap-4">
                @foreach ($developmentTeam as $member)
                    <div class="w-full max-w-xs sm:max-w-sm">
                        @include('partials.about-team-member', [
                            'stem' => $member['stem'],
                            'name' => $member['name'],
                            'role' => $member['role'],
                            'bio' => $member['bio'],
                            'imageUrl' => $resolveAboutImage($member['stem']),
                        ])
                    </div>
                @endforeach
            </div>
        </section>

        <div class="rounded-xl border border-dashed border-brand-soft bg-white/70 px-4 py-4 text-sm text-brand-medium">
            <p class="font-semibold text-brand-dark">Adding team photos</p>
            <p class="mt-2">
                Save images under <code class="rounded bg-brand-soft/80 px-1.5 py-0.5 text-xs text-brand-dark">public/team-photos/</code>
                (not <code class="text-xs">public/about/</code>—that path conflicts with the <code class="text-xs">/about</code> page URL).
                Use these base names (any of <code class="text-xs">.jpg</code>, <code class="text-xs">.jpeg</code>, <code class="text-xs">.png</code>, <code class="text-xs">.webp</code>):
            </p>
            <ul class="mt-2 list-inside list-disc text-xs leading-relaxed md:text-sm">
                <li><code>mayor</code>, <code>tourism-head</code></li>
                <li>Development team: <code>programmer-1</code>, <code>programmer-2</code>, <code>programmer-3</code>, <code>ux-ui-designer</code>, <code>documentor</code></li>
            </ul>
            <p class="mt-2">
                Set each person’s display name in <code class="rounded bg-brand-soft/80 px-1 text-xs">resources/views/about.blade.php</code>
                (<code class="text-xs">'name' => 'Full Name'</code> per entry).
            </p>
        </div>
    </main>

    @include('partials.central-public-footer')
</body>
</html>
