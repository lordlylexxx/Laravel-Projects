{{-- Owner onboarding payment (/owner/onboarding/payment); empty fields use IMPASTAY_ONBOARDING_GCASH_* from .env --}}
{{-- Uses <details> so expand/collapse works without Alpine (reliable on admin Tulogans page). --}}
@if(isset($gcashSetting) && $gcashSetting)
    @php
        $gcashPanelOpen = $errors->has('gcash_account_name') || $errors->has('gcash_number') || $errors->has('gcash_qr');
    @endphp
    <details class="card overflow-hidden gcash-settings-card gcash-details" style="margin-bottom: 1.25rem;" @if($gcashPanelOpen) open @endif>
        <summary class="card-header gcash-settings-header cursor-pointer select-none list-none" style="border-bottom: 1px solid var(--green-soft, #e8f5e9);">
            <div class="flex w-full flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="min-w-0 flex-1">
                    <h3 class="flex items-center gap-2 text-base font-semibold text-gray-900">
                        <i class="fas fa-qrcode shrink-0 text-emerald-600"></i>
                        <span>Owner onboarding — GCash</span>
                    </h3>
                    <p class="mt-1 max-w-2xl text-xs leading-relaxed text-gray-500">
                        Click this bar to show or hide settings. Shown on the owner <strong>onboarding payment</strong> page. Leave blank to use <code class="rounded bg-gray-100 px-1 py-0.5 text-[10px]">.env</code> defaults.
                    </p>
                </div>
                <div class="flex shrink-0 items-center gap-3 sm:pl-4">
                    <span class="gcash-panel-status text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <span class="gcash-status-collapsed">Collapsed</span>
                        <span class="gcash-status-expanded">Expanded</span>
                    </span>
                    <span class="gcash-switch" aria-hidden="true">
                        <span class="gcash-switch-track"></span>
                        <span class="gcash-switch-knob"></span>
                    </span>
                </div>
            </div>
        </summary>

        <div class="gcash-settings-body border-t border-gray-100 bg-white" style="padding: 18px 20px;">
            @if ($errors->has('gcash_account_name') || $errors->has('gcash_number') || $errors->has('gcash_qr'))
                <div class="flash-error" style="margin-bottom:12px;">
                    <ul class="m-0 pl-5 text-sm">
                        @foreach (['gcash_account_name', 'gcash_number', 'gcash_qr'] as $f)
                            @if ($errors->has($f))
                                <li>{{ $errors->first($f) }}</li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.tenants.onboarding-gcash.update', [], false) }}" enctype="multipart/form-data" class="gcash-settings-form space-y-4">
                @csrf
                @method('PATCH')

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-bold uppercase tracking-wide text-gray-500" for="gcash_account_name">Account display name</label>
                        <input id="gcash_account_name" name="gcash_account_name" type="text" value="{{ old('gcash_account_name', $gcashSetting->gcash_account_name) }}"
                               class="w-full rounded-md border border-gray-200 px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500/30"
                               placeholder="e.g. ImpaStay">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-bold uppercase tracking-wide text-gray-500" for="gcash_number">GCash number</label>
                        <input id="gcash_number" name="gcash_number" type="text" value="{{ old('gcash_number', $gcashSetting->gcash_number) }}"
                               class="w-full rounded-md border border-gray-200 px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500/30"
                               placeholder="09xx xxx xxxx">
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-xs font-bold uppercase tracking-wide text-gray-500" for="gcash_qr">QR code image</label>
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start">
                        @if($gcashSetting->gcash_qr_path)
                            <div class="gcash-qr-preview shrink-0">
                                <p class="mb-1.5 text-center text-[10px] font-semibold uppercase tracking-wide text-gray-500">Current</p>
                                <div class="gcash-qr-frame">
                                    <img src="{{ asset('storage/'.$gcashSetting->gcash_qr_path) }}" alt="GCash QR preview" width="132" height="132" loading="lazy">
                                </div>
                                <label class="mt-2 flex cursor-pointer items-center justify-center gap-2 text-xs text-gray-700">
                                    <input type="checkbox" name="remove_gcash_qr" value="1" {{ old('remove_gcash_qr') ? 'checked' : '' }} class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                    Remove image
                                </label>
                            </div>
                        @endif
                        <div class="min-w-0 flex-1">
                            <label for="gcash_qr" class="mb-2 inline-flex cursor-pointer items-center gap-2 rounded-lg border border-dashed border-gray-300 bg-gray-50 px-3 py-2.5 text-sm font-medium text-gray-700 transition hover:border-emerald-400 hover:bg-emerald-50/50">
                                <i class="fas fa-upload text-emerald-600"></i>
                                <span>Choose file</span>
                            </label>
                            <input id="gcash_qr" name="gcash_qr" type="file" accept=".jpg,.jpeg,.png,.gif,.webp,.bmp,image/*"
                                   class="gcash-file-input block w-full max-w-md text-xs text-gray-600 file:mr-3 file:rounded-md file:border-0 file:bg-emerald-600 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-white hover:file:bg-emerald-700">
                            <p class="mt-1.5 text-xs text-gray-500">JPG, PNG, GIF, WEBP, BMP — max 10&nbsp;MB. New upload replaces the current QR.</p>
                        </div>
                    </div>
                </div>

                <div class="pt-1">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-1">
                        <i class="fas fa-save"></i> Save GCash settings
                    </button>
                </div>
            </form>
        </div>
    </details>

    <style>
        .gcash-details > summary {
            list-style: none;
        }
        .gcash-details > summary::-webkit-details-marker {
            display: none;
        }
        .gcash-details .gcash-status-expanded {
            display: none;
        }
        .gcash-details[open] .gcash-status-collapsed {
            display: none;
        }
        .gcash-details[open] .gcash-status-expanded {
            display: inline;
        }
        .gcash-switch {
            position: relative;
            display: inline-block;
            width: 48px;
            height: 28px;
            flex-shrink: 0;
        }
        .gcash-switch-track {
            position: absolute;
            inset: 0;
            border-radius: 9999px;
            background: #d1d5db;
            transition: background-color 0.2s ease;
        }
        .gcash-details[open] .gcash-switch-track {
            background: #059669;
        }
        .gcash-switch-knob {
            position: absolute;
            top: 4px;
            left: 4px;
            width: 20px;
            height: 20px;
            border-radius: 9999px;
            background: #fff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
            transition: transform 0.2s ease;
        }
        .gcash-details[open] .gcash-switch-knob {
            transform: translateX(20px);
        }
        .gcash-qr-frame {
            width: 132px;
            height: 132px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            background: #fff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }
        .gcash-qr-frame img {
            max-width: 120px;
            max-height: 120px;
            width: auto;
            height: auto;
            object-fit: contain;
        }
    </style>
@endif
