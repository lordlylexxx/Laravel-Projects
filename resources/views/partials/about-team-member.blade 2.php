@php
    $displayName = isset($name) && $name !== '' && $name !== null ? $name : '—';
@endphp
<article class="overflow-hidden rounded-xl border border-brand-soft bg-white/90 shadow-md backdrop-blur-sm">
    @if (!empty($imageUrl))
        <img
            src="{{ $imageUrl }}"
            alt="{{ $displayName !== '—' ? $displayName : $role }}"
            class="h-36 w-full object-cover sm:h-40 md:h-44"
            loading="lazy"
        >
    @else
        <div class="flex h-36 w-full flex-col items-center justify-center bg-gradient-to-b from-brand-soft/90 to-white px-3 text-brand-medium sm:h-40 md:h-44">
            <i class="fa-solid fa-image mb-1 text-2xl opacity-40"></i>
            <span class="text-center text-[0.7rem] font-semibold">Photo coming soon</span>
            <span class="mt-1 hidden text-center text-[0.6rem] opacity-80 sm:block">public/team-photos/{{ $stem ?? '…' }}.jpg</span>
        </div>
    @endif
    <div class="p-3 sm:p-4">
        <p class="text-base font-bold leading-tight text-brand-dark">{{ $displayName }}</p>
        <p class="mt-0.5 text-[0.65rem] font-semibold uppercase tracking-wide text-brand-primary">{{ $role }}</p>
        <p class="mt-2 text-xs leading-snug text-brand-medium">{{ $bio }}</p>
    </div>
</article>
