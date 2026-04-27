<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('partials.tenant-favicon')
    <title>Edit Accommodation - ImpaStay</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            @include('partials.tenant-theme-css-vars')
            --red-light: #fee2e2; --red-dark: #991b1b;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--green-white) 0%, #F1F8E9 100%);
            min-height: 100vh;
            color: #1f2937;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: #fff;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 8px 24px rgba(27, 94, 32, 0.1);
        }
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid var(--green-soft);
        }
        .page-header h1 {
            margin: 0;
            color: var(--green-dark);
            font-size: 1.8rem;
        }
        .page-header a {
            color: var(--green-primary);
            text-decoration: none;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .page-header a:hover { color: var(--green-dark); }
        .section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--green-dark);
            margin: 24px 0 16px 0;
            padding-top: 16px;
            border-top: 1px solid #e5e7eb;
        }
        .grid { display: grid; gap: 16px; grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .full { grid-column: 1 / -1; }
        label { display: block; font-weight: 600; margin-bottom: 8px; color: #374151; }
        input, select, textarea {
            width: 100%;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 10px 12px;
            font-size: 14px;
            font-family: inherit;
        }
        textarea { min-height: 110px; }
        .error-list { background: var(--red-light); color: var(--red-dark); border: 1px solid #fecaca; border-radius: 8px; padding: 12px 14px; margin-bottom: 18px; }
        .hint { color: #6b7280; font-size: 13px; margin-top: 4px; }
        .check-row { display: flex; align-items: center; gap: 8px; }
        .check-row input { width: auto; }
        .amenities-grid { display: grid; gap: 8px 12px; grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .amenity-item { display: flex; align-items: center; gap: 8px; font-weight: 500; color: #374151; }
        .amenity-item input { width: auto; }
        .actions { margin-top: 24px; display: flex; gap: 12px; justify-content: flex-end; }
        .btn {
            border: 0;
            border-radius: 8px;
            padding: 11px 20px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.95rem;
        }
        .btn-primary { background: var(--green-primary); color: #fff; }
        .btn-primary:hover { background: var(--green-dark); }
        .btn-secondary { background: #e5e7eb; color: #111827; }
        .btn-secondary:hover { background: #d1d5db; }
        @media (max-width: 768px) {
            .grid { grid-template-columns: 1fr; }
            .container { padding: 20px; }
            .page-header { flex-direction: column; gap: 12px; align-items: flex-start; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h1><i class="fas fa-pen-to-square"></i> Edit Accommodation</h1>
            <a href="/owner/accommodations"><i class="fas fa-arrow-left"></i> Back to Listings</a>
        </div>

        @if ($errors->any())
            <div class="error-list">
                <strong>Please fix the following:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="/owner/accommodations/{{ $accommodation->id }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="section-title">
                <i class="fas fa-info-circle"></i> Property Information
            </div>

            <div class="grid">
                <div>
                    <label for="name">Property Name *</label>
                    <input id="name" name="name" type="text" value="{{ old('name', $accommodation->name) }}" required>
                </div>

                <div>
                    <label for="type">Property Type *</label>
                    <select id="type" name="type" required>
                        <option value="traveller-inn" @selected(old('type', $accommodation->type) === 'traveller-inn')>Traveller-Inn</option>
                        <option value="airbnb" @selected(old('type', $accommodation->type) === 'airbnb')>Airbnb</option>
                        <option value="daily-rental" @selected(old('type', $accommodation->type) === 'daily-rental')>Daily Rental</option>
                    </select>
                </div>

                <div class="full">
                    <label for="description">Description *</label>
                    <textarea id="description" name="description" required>{{ old('description', $accommodation->description) }}</textarea>
                </div>

                <div>
                    <label for="address">Address *</label>
                    <input id="address" name="address" type="text" value="{{ old('address', $accommodation->address) }}" required>
                </div>

                <div>
                    <label for="barangay">Barangay *</label>
                    <input id="barangay" name="barangay" type="text" value="{{ old('barangay', $accommodation->barangay) }}" required>
                </div>
            </div>

            <div class="section-title">
                <i class="fas fa-tag"></i> Pricing
            </div>

            <div class="grid">

                <div>
                    <label for="price_per_night">Price per Night (₱) *</label>
                    <input id="price_per_night" name="price_per_night" type="number" min="0" step="0.01" value="{{ old('price_per_night', $accommodation->price_per_night) }}" required>
                </div>

                <div>
                    <label for="price_per_day">Price per Day (₱)</label>
                    <input id="price_per_day" name="price_per_day" type="number" min="0" step="0.01" value="{{ old('price_per_day', $accommodation->price_per_day) }}">
                    <div class="hint">Optional: If different from nightly rate</div>
                </div>
            </div>

            <div class="section-title">
                <i class="fas fa-bed"></i> Details
            </div>

            <div class="grid">

                <div>
                    <label for="bedrooms">Bedrooms</label>
                    <input id="bedrooms" name="bedrooms" type="number" min="0" value="{{ old('bedrooms', $accommodation->bedrooms) }}">
                </div>

                <div>
                    <label for="bathrooms">Bathrooms</label>
                    <input id="bathrooms" name="bathrooms" type="number" min="0" value="{{ old('bathrooms', $accommodation->bathrooms) }}">
                </div>

                <div>
                    <label for="max_guests">Max Guests *</label>
                    <input id="max_guests" name="max_guests" type="number" min="1" value="{{ old('max_guests', $accommodation->max_guests ?? 1) }}" required>
                </div>

                <div>
                    <label for="primary_image">Primary Image</label>
                    <input id="primary_image" name="primary_image" type="file" accept="image/*">
                    <div class="hint">Leave empty to keep the current image.</div>
                </div>

                <div>
                    <label for="images">Room Photos (Multiple)</label>
                    <input id="images" name="images[]" type="file" accept="image/*" multiple>
                    <div class="hint">Upload up to 10 photos. Uploading new photos replaces the current gallery.</div>
                </div>

                <div class="full">
                    <label>Basic Amenities</label>
                    @php
                        $basicAmenities = [
                            'WiFi',
                            'Air Conditioning',
                            'Kitchen',
                            'Parking',
                            'TV',
                            'Hot Shower',
                            'Refrigerator',
                            'Toiletries',
                        ];
                        $amenitiesValue = old('amenities', $accommodation->amenities ?? []);
                        if (is_string($amenitiesValue)) {
                            $amenitiesValue = preg_split('/\r\n|\r|\n|,/', $amenitiesValue) ?: [];
                        }
                        $selectedAmenities = collect($amenitiesValue)
                            ->map(fn ($item) => trim((string) $item))
                            ->filter()
                            ->values()
                            ->all();
                    @endphp
                    <div class="amenities-grid">
                        @foreach($basicAmenities as $amenity)
                            <label class="amenity-item">
                                <input type="checkbox" name="amenities[]" value="{{ $amenity }}" {{ in_array($amenity, $selectedAmenities, true) ? 'checked' : '' }}>
                                <span>{{ $amenity }}</span>
                            </label>
                        @endforeach
                    </div>
                    <div class="hint">Select all amenities available in this property.</div>
                </div>

                <div class="full">
                    <label for="house_rules">House Rules</label>
                    <textarea id="house_rules" name="house_rules">{{ old('house_rules', $accommodation->house_rules) }}</textarea>
                </div>

                <div class="full">
                    <label for="check_in_instructions">Check-in Instructions</label>
                    <textarea id="check_in_instructions" name="check_in_instructions">{{ old('check_in_instructions', $accommodation->check_in_instructions) }}</textarea>
                </div>

                <div class="full check-row">
                    <input id="is_available" name="is_available" type="checkbox" value="1" @checked(old('is_available', $accommodation->is_available))>
                    <label for="is_available" style="margin: 0;">Set as available</label>
                </div>
            </div>

            <div class="actions">
                <a href="/owner/accommodations" class="btn btn-secondary"><i class="fas fa-xmark"></i> Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-check"></i> Save Changes</button>
            </div>
        </form>
    </div>
</body>
</html>
