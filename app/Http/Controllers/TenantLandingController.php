<?php

namespace App\Http\Controllers;

use App\Models\Accommodation;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TenantLandingController extends Controller
{
    /**
     * Show the public landing page for the current tenant subdomain.
     */
    public function showPublic(Request $request): View
    {
        $tenant = Tenant::current();

        abort_unless($tenant, 404);

        $settings = $tenant->landingSettings();

        $featuredAccommodations = Accommodation::query()
            ->featured()
            ->latest('id')
            ->take(8)
            ->get();

        if ($featuredAccommodations->isEmpty()) {
            $featuredAccommodations = Accommodation::query()
                ->available()
                ->latest('id')
                ->take(8)
                ->get();
        }

        return view('tenant.landing', compact('tenant', 'settings', 'featuredAccommodations'));
    }

    /**
     * Show owner settings for tenant landing customization.
     */
    public function edit(Request $request): View
    {
        $tenant = Tenant::current();

        if (! $tenant) {
            $tenant = $request->user()->ensureTenant();
        }

        abort_unless($tenant, 404);

        $settings = $tenant->landingSettings();

        return view('owner.landing-settings', compact('tenant', 'settings'));
    }

    /**
     * Persist owner landing page customization.
     */
    public function update(Request $request): RedirectResponse
    {
        $tenant = Tenant::current();

        if (! $tenant) {
            $tenant = $request->user()->ensureTenant();
        }

        abort_unless($tenant, 404);

        $validated = $request->validate([
            'hero_title' => ['required', 'string', 'max:120'],
            'hero_subtitle' => ['required', 'string', 'max:255'],
            'cta_text' => ['required', 'string', 'max:40'],
            'cta_url' => ['required', 'string', 'max:255'],
            'login_section_title' => ['required', 'string', 'max:100'],
            'login_section_subtitle' => ['required', 'string', 'max:200'],
            'login_text' => ['required', 'string', 'max:40'],
            'signup_text' => ['required', 'string', 'max:40'],
            'about_title' => ['required', 'string', 'max:80'],
            'about_text' => ['required', 'string', 'max:600'],
            'primary_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'accent_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'hero_image_url' => ['nullable', 'url', 'max:500'],
        ]);

        $tenant->updateLandingSettings($validated);

        return back()->with('success', 'Landing page settings updated successfully.');
    }
}
