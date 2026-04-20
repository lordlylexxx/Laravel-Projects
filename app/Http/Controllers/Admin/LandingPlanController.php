<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CentralLandingPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LandingPlanController extends Controller
{
    public function index(): View
    {
        $plans = CentralLandingPlan::query()->orderBy('sort_order')->orderBy('id')->get();

        return view('admin.landing-plans.index', compact('plans'));
    }

    public function create(): View
    {
        return view('admin.landing-plans.edit', [
            'plan' => new CentralLandingPlan([
                'is_visible' => true,
                'is_featured' => false,
                'aggregate_catalog_features' => false,
                'sort_order' => (int) (CentralLandingPlan::query()->max('sort_order') ?? -1) + 1,
            ]),
            'isCreate' => true,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatedPayload($request);

        if ($validated['is_featured'] ?? false) {
            CentralLandingPlan::query()->update(['is_featured' => false]);
        }

        CentralLandingPlan::query()->create($validated);

        return redirect()->route('admin.landing-plans.index')->with('success', 'Landing plan created.');
    }

    public function edit(CentralLandingPlan $central_landing_plan): View
    {
        return view('admin.landing-plans.edit', [
            'plan' => $central_landing_plan,
            'isCreate' => false,
        ]);
    }

    public function update(Request $request, CentralLandingPlan $central_landing_plan): RedirectResponse
    {
        $validated = $this->validatedPayload($request);

        if ($request->boolean('clear_catalog_overrides')) {
            $validated['features'] = null;
            $validated['aggregate_catalog_features'] = false;
        }

        if ($validated['is_featured'] ?? false) {
            CentralLandingPlan::query()->whereKeyNot($central_landing_plan->getKey())->update(['is_featured' => false]);
        }

        $central_landing_plan->update($validated);

        return redirect()->route('admin.landing-plans.index')->with('success', 'Landing plan updated.');
    }

    public function destroy(CentralLandingPlan $central_landing_plan): RedirectResponse
    {
        $central_landing_plan->delete();

        return redirect()->route('admin.landing-plans.index')->with('success', 'Landing plan removed.');
    }

    public function toggleVisibility(CentralLandingPlan $central_landing_plan): RedirectResponse
    {
        $nowVisible = ! $central_landing_plan->is_visible;
        $central_landing_plan->update(['is_visible' => $nowVisible]);

        $message = $nowVisible
            ? 'Plan is now visible on the CA landing.'
            : 'Plan is hidden from the CA landing.';

        return redirect()->route('admin.landing-plans.index')->with('success', $message);
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedPayload(Request $request): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'tenant_plan_key' => ['required', 'string', 'in:'.implode(',', CentralLandingPlan::ALLOWED_PLAN_KEYS)],
            'feature_mode' => ['required', 'string', 'in:tier_catalog,full_catalog,custom_pick'],
            'feature_pick' => ['nullable', 'array'],
            'feature_pick.*' => ['string', 'max:255'],
            'is_visible' => ['sometimes', 'boolean'],
            'is_featured' => ['sometimes', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:32767'],
            'price_amount' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
        ]);

        $mode = $validated['feature_mode'];
        unset($validated['feature_mode']);

        $allowed = CentralLandingPlan::mergedTierCatalogFeatureLines();
        if ($mode === 'tier_catalog') {
            $validated['aggregate_catalog_features'] = false;
            $validated['features'] = null;
        } elseif ($mode === 'full_catalog') {
            $validated['aggregate_catalog_features'] = true;
            $validated['features'] = null;
        } else {
            $validated['aggregate_catalog_features'] = false;
            $picked = array_values(array_unique($validated['feature_pick'] ?? []));
            $ordered = [];
            foreach ($allowed as $line) {
                if (in_array($line, $picked, true)) {
                    $ordered[] = $line;
                }
            }
            if ($ordered === []) {
                throw ValidationException::withMessages([
                    'feature_pick' => 'Select at least one feature when using “Choose bullets”.',
                ]);
            }
            $validated['features'] = $ordered;
        }
        unset($validated['feature_pick']);

        $validated['is_visible'] = $request->boolean('is_visible');
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['button_label'] = null;
        $validated['title'] = trim((string) $validated['title']);

        $price = $validated['price_amount'] ?? null;
        $validated['price_amount'] = ($price === null || $price === '')
            ? null
            : round((float) $price, 2);

        return $validated;
    }
}
