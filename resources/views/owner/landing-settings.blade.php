<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('partials.tenant-favicon')
    <title>Landing Page Settings</title>
    <style>
        :root {
            --primary: #14532d;
            --accent: #16a34a;
            --paper: #f1f5f9;
            --ink: #111827;
            --muted: #6b7280;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: "Trebuchet MS", Arial, sans-serif;
            background: linear-gradient(140deg, #ffffff 0%, var(--paper) 100%);
            color: var(--ink);
            min-height: 100vh;
        }

        .wrap {
            max-width: 860px;
            margin: 0 auto;
            padding: 28px 18px 40px;
        }

        .card {
            background: #ffffff;
            border-radius: 18px;
            padding: 24px;
            box-shadow: 0 16px 35px rgba(0, 0, 0, 0.08);
        }

        h1 {
            margin: 0 0 8px;
            font-size: 1.6rem;
        }

        .muted {
            color: var(--muted);
            margin: 0 0 18px;
        }

        .row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .field {
            margin-bottom: 12px;
        }

        label {
            display: block;
            font-size: 0.88rem;
            font-weight: 700;
            margin-bottom: 6px;
        }

        input, textarea {
            width: 100%;
            border: 1px solid #d1d5db;
            border-radius: 10px;
            padding: 10px 12px;
            font-size: 0.94rem;
        }

        textarea {
            min-height: 100px;
            resize: vertical;
        }

        .actions {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-top: 8px;
        }

        .btn {
            border: none;
            border-radius: 10px;
            padding: 11px 16px;
            font-weight: 700;
            cursor: pointer;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--accent));
            color: white;
        }

        .btn-link {
            text-decoration: none;
            color: var(--primary);
            font-weight: 700;
        }

        .ok {
            background: #ecfdf5;
            border: 1px solid #86efac;
            color: #166534;
            padding: 8px 10px;
            border-radius: 8px;
            margin-bottom: 14px;
            font-size: 0.9rem;
        }

        .error {
            color: #b91c1c;
            font-size: 0.83rem;
            margin-top: 4px;
        }

        @media (max-width: 760px) {
            .row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="card">
            <h1>Tenant Landing Customization</h1>
            <p class="muted">Customize how your public subdomain landing page looks.</p>

            @if(session('success'))
                <div class="ok">{{ session('success') }}</div>
            @endif

            <form method="POST" action="{{ route('owner.landing.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="field">
                    <label>Hero Title</label>
                    <input type="text" name="hero_title" value="{{ old('hero_title', $settings['hero_title']) }}">
                    @error('hero_title') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="field">
                    <label>Hero Subtitle</label>
                    <input type="text" name="hero_subtitle" value="{{ old('hero_subtitle', $settings['hero_subtitle']) }}">
                    @error('hero_subtitle') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="row">
                    <div class="field">
                        <label>Call To Action Text</label>
                        <input type="text" name="cta_text" value="{{ old('cta_text', $settings['cta_text']) }}">
                        @error('cta_text') <div class="error">{{ $message }}</div> @enderror
                    </div>
                    <div class="field">
                        <label>Call To Action URL</label>
                        <input type="text" name="cta_url" value="{{ old('cta_url', $settings['cta_url']) }}">
                        @error('cta_url') <div class="error">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="field">
                    <label>Login Section Title</label>
                    <input type="text" name="login_section_title" value="{{ old('login_section_title', $settings['login_section_title']) }}">
                    @error('login_section_title') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="field">
                    <label>Login Section Subtitle</label>
                    <input type="text" name="login_section_subtitle" value="{{ old('login_section_subtitle', $settings['login_section_subtitle']) }}">
                    @error('login_section_subtitle') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="row">
                    <div class="field">
                        <label>Login Button Text</label>
                        <input type="text" name="login_text" value="{{ old('login_text', $settings['login_text']) }}">
                        @error('login_text') <div class="error">{{ $message }}</div> @enderror
                    </div>
                    <div class="field">
                        <label>Sign Up Button Text</label>
                        <input type="text" name="signup_text" value="{{ old('signup_text', $settings['signup_text']) }}">
                        @error('signup_text') <div class="error">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="field">
                        <label>Primary Color</label>
                        <input type="text" name="primary_color" value="{{ old('primary_color', $settings['primary_color']) }}" placeholder="#14532d">
                        @error('primary_color') <div class="error">{{ $message }}</div> @enderror
                    </div>
                    <div class="field">
                        <label>Accent Color</label>
                        <input type="text" name="accent_color" value="{{ old('accent_color', $settings['accent_color']) }}" placeholder="#16a34a">
                        @error('accent_color') <div class="error">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="field">
                    <label>Hero Image URL (optional)</label>
                    <input type="url" name="hero_image_url" value="{{ old('hero_image_url', $settings['hero_image_url']) }}">
                    @error('hero_image_url') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="field" style="margin-top:14px; padding-top:14px; border-top:1px solid #e5e7eb;">
                    <label>GCash QR Code (optional)</label>
                    @if($tenant->getGcashQrUrl())
                        <div style="margin-bottom:10px;">
                            <img src="{{ $tenant->getGcashQrUrl() }}" alt="GCash QR" style="max-width:220px; border:1px solid #d1d5db; border-radius:10px; padding:6px; background:#fff;">
                        </div>
                    @endif
                    <input type="file" name="gcash_qr" accept=".png,.jpg,.jpeg,.webp,image/png,image/jpeg,image/webp">
                    @error('gcash_qr') <div class="error">{{ $message }}</div> @enderror

                    @if($tenant->gcash_qr_path)
                        <label style="display:flex; align-items:center; gap:8px; margin-top:10px; font-weight:600; font-size:0.85rem;">
                            <input type="checkbox" name="remove_gcash_qr" value="1" style="width:auto;">
                            Remove current GCash QR
                        </label>
                    @endif
                </div>

                <div class="field">
                    <label>About Title</label>
                    <input type="text" name="about_title" value="{{ old('about_title', $settings['about_title']) }}">
                    @error('about_title') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="field">
                    <label>About Description</label>
                    <textarea name="about_text">{{ old('about_text', $settings['about_text']) }}</textarea>
                    @error('about_text') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="actions">
                    <button class="btn btn-primary" type="submit">Save Settings</button>
                    <a class="btn-link" target="_blank" href="{{ $tenant->publicUrl() }}">Open Public Landing</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
