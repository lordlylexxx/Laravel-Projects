<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\TenantLifecycleLog;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
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
            return redirect()->route('owner.dashboard');
        }

        if ($tenant->onboarding_status !== Tenant::ONBOARDING_AWAITING_PAYMENT) {
            return redirect()->route('owner.onboarding.status');
        }

        $amount = $tenant->mockSubscriptionAmount();
        $planDetails = Tenant::getPlanDetails();
        $currency = $planDetails[$tenant->plan]['currency'] ?? ($planDetails[Tenant::PLAN_BASIC]['currency'] ?? '₱');
        $reference = $tenant->payment_reference ?: $this->buildPaymentReference($tenant);
        if (! $tenant->payment_reference) {
            $tenant->update(['payment_reference' => $reference]);
        }

        $payload = sprintf(
            'IMPASTAY|MOCK|TENANT:%d|REF:%s|AMOUNT:%s %s',
            $tenant->id,
            $reference,
            $currency,
            number_format($amount, 2, '.', '')
        );

        $options = new QROptions([
            'outputType' => QRCode::OUTPUT_MARKUP_SVG,
            // Library default is true; a data URI in a div renders as a long text line, not a QR image.
            'outputBase64' => false,
            'svgAddXmlHeader' => false,
        ]);

        $qrSvg = (new QRCode($options))->render($payload);

        return view('owner.onboarding.payment', [
            'tenant' => $tenant,
            'amount' => $amount,
            'currency' => $currency,
            'reference' => $reference,
            'qrSvg' => $qrSvg,
            'payload' => $payload,
        ]);
    }

    public function submitPayment(Request $request): RedirectResponse
    {
        $tenant = $this->resolveOwnedTenant($request);
        if (! $tenant instanceof Tenant) {
            abort(Response::HTTP_NOT_FOUND);
        }

        if ($tenant->onboarding_status !== Tenant::ONBOARDING_AWAITING_PAYMENT) {
            return redirect()->route('owner.onboarding.status');
        }

        $tenant->update([
            'payment_submitted_at' => now(),
            'onboarding_status' => Tenant::ONBOARDING_PENDING_APPROVAL,
        ]);

        TenantLifecycleLog::create([
            'tenant_id' => $tenant->id,
            'actor_user_id' => $request->user()?->id,
            'action' => 'tenant.payment.submitted',
            'reason' => 'Owner confirmed mock payment (demo).',
            'before_state' => [
                'onboarding_status' => Tenant::ONBOARDING_AWAITING_PAYMENT,
            ],
            'after_state' => [
                'onboarding_status' => Tenant::ONBOARDING_PENDING_APPROVAL,
                'payment_reference' => $tenant->payment_reference,
                'payment_submitted_at' => $tenant->payment_submitted_at?->toDateTimeString(),
            ],
        ]);

        return redirect()
            ->route('owner.onboarding.status')
            ->with('success', 'Payment marked as submitted. We will notify you when an administrator approves your space.');
    }

    public function status(Request $request): RedirectResponse|View
    {
        $tenant = $this->resolveOwnedTenant($request);
        if (! $tenant instanceof Tenant) {
            abort(Response::HTTP_NOT_FOUND);
        }

        if ($tenant->onboarding_status === Tenant::ONBOARDING_APPROVED) {
            return redirect()->route('owner.dashboard');
        }

        if ($tenant->onboarding_status === Tenant::ONBOARDING_AWAITING_PAYMENT) {
            return redirect()->route('owner.onboarding.payment');
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

    private function buildPaymentReference(Tenant $tenant): string
    {
        return 'TXN-'.$tenant->id.'-'.strtoupper(bin2hex(random_bytes(4)));
    }
}
