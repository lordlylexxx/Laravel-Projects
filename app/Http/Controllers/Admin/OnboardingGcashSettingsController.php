<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CentralOnboardingGcashSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OnboardingGcashSettingsController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $setting = CentralOnboardingGcashSetting::singleton();

        if ($request->hasFile('gcash_qr') && ! $request->file('gcash_qr')->isValid()) {
            $request->files->remove('gcash_qr');
        }

        $validated = $request->validate([
            'gcash_account_name' => ['nullable', 'string', 'max:255'],
            'gcash_number' => ['nullable', 'string', 'max:64'],
            'gcash_qr' => ['nullable', 'image', 'max:10240', 'mimes:jpeg,jpg,png,gif,webp,bmp'],
        ]);

        $removeQr = $request->boolean('remove_gcash_qr');

        $payload = [
            'gcash_account_name' => $validated['gcash_account_name'] ?? null,
            'gcash_number' => $validated['gcash_number'] ?? null,
        ];

        if ($removeQr && $setting->gcash_qr_path) {
            Storage::disk('public')->delete($setting->gcash_qr_path);
            $payload['gcash_qr_path'] = null;
        }

        if ($request->hasFile('gcash_qr')) {
            if ($setting->gcash_qr_path) {
                Storage::disk('public')->delete($setting->gcash_qr_path);
            }
            $payload['gcash_qr_path'] = $request->file('gcash_qr')->store('central-onboarding-gcash-qr', 'public');
        }

        $setting->update($payload);

        return redirect('/admin/tenants')
            ->with('success', 'Onboarding GCash settings saved.');
    }
}
