@php
    /** @var \App\Models\CentralLandingPlan $plan */
    $cardClass = $plan->is_featured
        ? 'rounded-[20px] border-2 border-brand-primary bg-white/15 p-8 shadow-[0_10px_40px_rgba(27,94,32,0.14)] backdrop-blur-sm transition-all duration-300 hover:-translate-y-2.5 hover:shadow-2xl flex h-full min-h-0 w-full flex-col gap-6'
        : 'rounded-[20px] border border-white/35 bg-white/15 p-8 backdrop-blur-sm transition-all duration-300 hover:-translate-y-2.5 hover:shadow-2xl flex h-full min-h-0 w-full flex-col gap-6';
@endphp
<div class="{{ $cardClass }}">
    <h3 class="text-xl font-bold text-brand-dark">{{ $plan->effectiveTitle() }}</h3>
    <div class="shrink-0 text-3xl font-extrabold text-brand-dark">
        @php $p = $plan->effectivePrice(); @endphp
        {{ $plan->effectiveCurrency() }}{{ number_format($p, floor($p) == $p ? 0 : 2) }}
    </div>
    <ul class="list-none min-h-0 flex-1 space-y-3 text-brand-dark" role="list">
        @foreach($plan->effectiveFeatures() as $line)
            <li class="flex items-start gap-3 text-[0.95rem] leading-snug">
                <span class="mt-0.5 inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-full border-2 border-brand-soft bg-white/80 text-brand-primary shadow-[0_1px_4px_rgba(27,94,32,0.12)]" aria-hidden="true">
                    <i class="fa-solid fa-check text-[0.65rem]"></i>
                </span>
                <span class="min-w-0 pt-0.5">{{ $line }}</span>
            </li>
        @endforeach
    </ul>
    <a href="{{ $plan->registerUrl() }}" class="mt-auto inline-flex w-full shrink-0 items-center justify-center rounded-xl border border-brand-primary bg-white/75 px-3.5 py-3 font-bold text-brand-primary transition-all hover:-translate-y-0.5 hover:bg-brand-primary hover:text-white">
        {{ $plan->effectiveButtonLabel() }}
    </a>
</div>
