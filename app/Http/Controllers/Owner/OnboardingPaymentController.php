<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\CentralOnboardingGcashSetting;
use App\Models\Tenant;
use App\Models\TenantLifecycleLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Stripe\Exception\AuthenticationException;
use Stripe\StripeClient;
use Symfony\Component\HttpFoundation\Response;

class OnboardingPaymentController extends Controller
{
    public function showPayment(Request $request): RedirectResponse|View
    {
        $tenant = $this->resolveOwnedTenant($request);
        if (! $tenant instanceof Tenant) {
            abort(Response::HTTP_NOT_FOUND);
        }

        if ($tenant->onboarding_status === Tenant::ONBOARDING_APPROVED) {
            return redirect(route('owner.dashboard', [], false));
        }

        if ($tenant->onboarding_status !== Tenant::ONBOARDING_AWAITING_PAYMENT) {
            return redirect(route('owner.onboarding.status', [], false));
        }

        $amount = $tenant->mockSubscriptionAmount();
        $planDetails = Tenant::getPlanDetails();
        $currency = $planDetails[$tenant->plan]['currency'] ?? ($planDetails[Tenant::PLAN_BASIC]['currency'] ?? '₱');
        $reference = $this->ensurePaymentReference($tenant);

        $gcashSetting = Schema::hasTable('central_onboarding_gcash_settings')
            ? CentralOnboardingGcashSetting::query()->first()
            : null;
        $onboardingGcashAccountName = filled(trim((string) ($gcashSetting?->gcash_account_name ?? '')))
            ? trim((string) $gcashSetting->gcash_account_name)
            : (string) config('impastay.onboarding_gcash_account_name', 'ImpaStay');
        $onboardingGcashNumber = filled(trim((string) ($gcashSetting?->gcash_number ?? '')))
            ? trim((string) $gcashSetting->gcash_number)
            : (string) (config('impastay.onboarding_gcash_number') ?? '');
        $onboardingGcashQrUrl = filled((string) ($gcashSetting?->gcash_qr_path ?? ''))
            ? asset('storage/'.$gcashSetting->gcash_qr_path)
            : null;

        return view('owner.onboarding.payment', [
            'tenant' => $tenant,
            'amount' => $amount,
            'currency' => $currency,
            'reference' => $reference,
            'onboardingGcashAccountName' => $onboardingGcashAccountName,
            'onboardingGcashNumber' => $onboardingGcashNumber,
            'onboardingGcashQrUrl' => $onboardingGcashQrUrl,
        ]);
    }

    public function startStripeCheckout(Request $request): RedirectResponse
    {
        $tenant = $this->resolveOwnedTenant($request);
        if (! $tenant instanceof Tenant) {
            abort(Response::HTTP_NOT_FOUND);
        }

        if ($tenant->onboarding_status !== Tenant::ONBOARDING_AWAITING_PAYMENT) {
            return redirect(route('owner.onboarding.status', [], false));
        }

        $amountInCentavos = (int) round($tenant->mockSubscriptionAmount() * 100);
        if ($amountInCentavos < 100) {
            return back()->with('error', 'Subscription amount is too low for Stripe checkout.');
        }

        $stripeSecret = (string) config('services.stripe.secret');
        if ($stripeSecret === '') {
            return back()->with('error', 'Stripe is not configured. Please contact support.');
        }

        try {
            $stripe = new StripeClient($stripeSecret);
            $session = $stripe->checkout->sessions->create([
                'mode' => 'payment',
                'success_url' => url(route('owner.onboarding.payment.stripe.success', [], false)).'?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => url(route('owner.onboarding.payment', [], false)),
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'php',
                        'product_data' => [
                            'name' => $tenant->name.' Subscription Onboarding',
                        ],
                        'unit_amount' => $amountInCentavos,
                    ],
                    'quantity' => 1,
                ]],
                'metadata' => [
                    'payment_type' => 'tenant_onboarding',
                    'tenant_id' => (string) $tenant->id,
                    'owner_user_id' => (string) ($request->user()?->id ?? ''),
                ],
            ]);

            return redirect()->away((string) $session->url);
        } catch (\Throwable $exception) {
            Log::error('Failed to create Stripe checkout for tenant onboarding.', [
                'tenant_id' => $tenant->id,
                'owner_user_id' => $request->user()?->id,
                'error' => $exception->getMessage(),
            ]);

            if ($exception instanceof AuthenticationException) {
                return back()->with('error', 'Stripe authentication failed. Please re-check STRIPE_SECRET.');
            }

            return back()->with('error', 'Unable to start Stripe checkout at the moment.');
        }
    }

    public function stripeSuccess(Request $request): RedirectResponse
    {
        $tenant = $this->resolveOwnedTenant($request);
        if (! $tenant instanceof Tenant) {
            abort(Response::HTTP_NOT_FOUND);
        }

        if ($tenant->onboarding_status !== Tenant::ONBOARDING_AWAITING_PAYMENT) {
            return redirect(route('owner.onboarding.status', [], false));
        }

        $validated = $request->validate([
            'session_id' => ['required', 'string'],
        ]);

        $stripeSecret = (string) config('services.stripe.secret');
        if ($stripeSecret === '') {
            return redirect(route('owner.onboarding.payment', [], false))->with('error', 'Stripe is not configured.');
        }

        try {
            $stripe = new StripeClient($stripeSecret);
            $session = $stripe->checkout->sessions->retrieve($validated['session_id']);
        } catch (\Throwable $exception) {
            Log::error('Failed to verify Stripe onboarding payment success session.', [
                'tenant_id' => $tenant->id,
                'session_id' => $validated['session_id'],
                'error' => $exception->getMessage(),
            ]);

            return redirect(route('owner.onboarding.payment', [], false))
                ->with('error', 'Payment completed but verification failed. Please try again.');
        }

        $sessionTenantId = (string) ($session->metadata->tenant_id ?? '');
        $sessionOwnerId = (string) ($session->metadata->owner_user_id ?? '');
        $paymentStatus = (string) ($session->payment_status ?? '');

        if (
            $sessionTenantId !== (string) $tenant->id
            || $sessionOwnerId !== (string) ($request->user()?->id ?? '')
            || $paymentStatus !== 'paid'
        ) {
            return redirect(route('owner.onboarding.payment', [], false))
                ->with('error', 'Stripe payment session validation failed.');
        }

        $this->markTenantPaymentSubmitted(
            tenant: $tenant,
            actorId: $request->user()?->id,
            channel: 'stripe',
            reason: 'Owner completed Stripe checkout for onboarding.',
            details: [
                'onboarding_stripe_session_id' => (string) ($session->id ?? ''),
                'payment_reference' => (string) ($session->payment_intent ?? $session->id ?? $tenant->payment_reference),
            ]
        );

        return redirect(route('owner.onboarding.status', [], false))
            ->with('success', 'Stripe payment verified. Your registration is now pending admin approval.');
    }

    public function submitGcashProof(Request $request): RedirectResponse
    {
        $tenant = $this->resolveOwnedTenant($request);
        if (! $tenant instanceof Tenant) {
            abort(Response::HTTP_NOT_FOUND);
        }

        if ($tenant->onboarding_status !== Tenant::ONBOARDING_AWAITING_PAYMENT) {
            return redirect(route('owner.onboarding.status', [], false));
        }

        $request->validate([
            'gcash_payment_proof' => ['required', 'image', 'max:5120', 'mimes:jpeg,jpg,png,webp'],
        ]);

        if ($tenant->onboarding_gcash_proof_path) {
            Storage::disk('public')->delete($tenant->onboarding_gcash_proof_path);
        }

        $proofPath = $request->file('gcash_payment_proof')->store('tenant-onboarding-payment-proofs', 'public');
        $reference = $this->ensurePaymentReference($tenant);

        $this->markTenantPaymentSubmitted(
            tenant: $tenant,
            actorId: $request->user()?->id,
            channel: 'gcash',
            reason: 'Owner uploaded GCash proof for onboarding.',
            details: [
                'onboarding_gcash_proof_path' => $proofPath,
                'onboarding_gcash_submitted_at' => now(),
                'payment_reference' => $reference,
            ]
        );

        return redirect(route('owner.onboarding.status', [], false))
            ->with('success', 'GCash payment proof submitted. We will notify you after admin review.');
    }

    public function status(Request $request): RedirectResponse|View
    {
        $tenant = $this->resolveOwnedTenant($request);
        if (! $tenant instanceof Tenant) {
            abort(Response::HTTP_NOT_FOUND);
        }

        if ($tenant->onboarding_status === Tenant::ONBOARDING_APPROVED) {
            return redirect(route('owner.dashboard', [], false));
        }

        if ($tenant->onboarding_status === Tenant::ONBOARDING_AWAITING_PAYMENT) {
            return redirect(route('owner.onboarding.payment', [], false));
        }

        $state = match ($tenant->onboarding_status) {
            Tenant::ONBOARDING_PENDING_APPROVAL => 'pending',
            Tenant::ONBOARDING_REJECTED => 'rejected',
            default => 'pending',
        };

        return view('owner.onboarding.status', [
            'tenant' => $tenant,
            'state' => $state,
        ]);
    }

    private function resolveOwnedTenant(Request $request): ?Tenant
    {
        $user = $request->user();

        return $user?->ownedTenant;
    }

    private function ensurePaymentReference(Tenant $tenant): string
    {
        if ($tenant->payment_reference) {
            return (string) $tenant->payment_reference;
        }

        $reference = $this->buildPaymentReference($tenant);
        $tenant->update(['payment_reference' => $reference]);

        return $reference;
    }

    private function markTenantPaymentSubmitted(
        Tenant $tenant,
        ?int $actorId,
        string $channel,
        string $reason,
        array $details = []
    ): void {
        $before = [
            'onboarding_status' => (string) $tenant->onboarding_status,
            'payment_reference' => $tenant->payment_reference,
            'onboarding_payment_channel' => $tenant->onboarding_payment_channel,
        ];

        $updatePayload = array_merge([
            'payment_submitted_at' => now(),
            'onboarding_status' => Tenant::ONBOARDING_PENDING_APPROVAL,
            'onboarding_payment_channel' => $channel,
        ], $details);

        $tenant->update($updatePayload);
        $tenant->refresh();

        TenantLifecycleLog::create([
            'tenant_id' => $tenant->id,
            'actor_user_id' => $actorId,
            'action' => 'tenant.payment.submitted',
            'reason' => $reason,
            'before_state' => $before,
            'after_state' => [
                'onboarding_status' => $tenant->onboarding_status,
                'payment_reference' => $tenant->payment_reference,
                'payment_submitted_at' => $tenant->payment_submitted_at?->toDateTimeString(),
                'onboarding_payment_channel' => $tenant->onboarding_payment_channel,
                'onboarding_gcash_proof_path' => $tenant->onboarding_gcash_proof_path,
                'onboarding_stripe_session_id' => $tenant->onboarding_stripe_session_id,
            ],
        ]);
    }

    private function buildPaymentReference(Tenant $tenant): string
    {
        return 'TXN-'.$tenant->id.'-'.strtoupper(bin2hex(random_bytes(4)));
    }
}
