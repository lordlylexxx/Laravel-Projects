<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Accommodation</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f7f6; color: #1f2937; margin: 0; }
        .container { max-width: 920px; margin: 24px auto; background: #fff; border-radius: 12px; padding: 24px; box-shadow: 0 8px 24px rgba(0,0,0,0.08); }
        h1 { margin-top: 0; color: #1b5e20; }
        .grid { display: grid; gap: 16px; grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .full { grid-column: 1 / -1; }
        label { display: block; font-weight: 600; margin-bottom: 6px; }
        input, select, textarea { width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 10px 12px; font-size: 14px; }
        textarea { min-height: 110px; }
        .actions { margin-top: 18px; display: flex; gap: 12px; }
        .btn { border: 0; border-radius: 8px; padding: 10px 16px; cursor: pointer; font-weight: 600; text-decoration: none; display: inline-block; }
        .btn-primary { background: #2e7d32; color: #fff; }
        .btn-secondary { background: #e5e7eb; color: #111827; }
        .error-list { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; border-radius: 8px; padding: 10px 12px; margin-bottom: 14px; }
        .hint { color: #6b7280; font-size: 13px; margin-top: 4px; }
        .check-row { display: flex; align-items: center; gap: 8px; }
        .check-row input { width: auto; }
        @media (max-width: 768px) { .grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Accommodation</h1>

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

        <form action="{{ route('owner.accommodations.update', $accommodation) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid">
                <div>
                    <label for="name">Name</label>
                    <input id="name" name="name" type="text" value="{{ old('name', $accommodation->name) }}" required>
                </div>

                <div>
                    <label for="type">Type</label>
                    <select id="type" name="type" required>
                        <option value="traveller-inn" @selected(old('type', $accommodation->type) === 'traveller-inn')>Traveller-Inn</option>
                        <option value="airbnb" @selected(old('type', $accommodation->type) === 'airbnb')>Airbnb</option>
                        <option value="daily-rental" @selected(old('type', $accommodation->type) === 'daily-rental')>Daily Rental</option>
                    </select>
                </div>

                <div class="full">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" required>{{ old('description', $accommodation->description) }}</textarea>
                </div>

                <div>
                    <label for="address">Address</label>
                    <input id="address" name="address" type="text" value="{{ old('address', $accommodation->address) }}" required>
                </div>

                <div>
                    <label for="barangay">Barangay</label>
                    <input id="barangay" name="barangay" type="text" value="{{ old('barangay', $accommodation->barangay) }}" required>
                </div>

                <div>
                    <label for="price_per_night">Price per Night</label>
                    <input id="price_per_night" name="price_per_night" type="number" min="0" step="0.01" value="{{ old('price_per_night', $accommodation->price_per_night) }}" required>
                </div>

                <div>
                    <label for="price_per_day">Price per Day</label>
                    <input id="price_per_day" name="price_per_day" type="number" min="0" step="0.01" value="{{ old('price_per_day', $accommodation->price_per_day) }}">
                </div>

                <div>
                    <label for="bedrooms">Bedrooms</label>
                    <input id="bedrooms" name="bedrooms" type="number" min="0" value="{{ old('bedrooms', $accommodation->bedrooms) }}">
                </div>

                <div>
                    <label for="bathrooms">Bathrooms</label>
                    <input id="bathrooms" name="bathrooms" type="number" min="0" value="{{ old('bathrooms', $accommodation->bathrooms) }}">
                </div>

                <div>
                    <label for="max_guests">Max Guests</label>
                    <input id="max_guests" name="max_guests" type="number" min="1" value="{{ old('max_guests', $accommodation->max_guests) }}">
                </div>

                <div>
                    <label for="primary_image">Primary Image</label>
                    <input id="primary_image" name="primary_image" type="file" accept="image/*">
                    <div class="hint">Leave empty to keep the current image.</div>
                </div>

                <div class="full">
                    <label for="amenities">Amenities (one per line)</label>
                    @php($amenitiesValue = old('amenities', $accommodation->amenities ?? []))
                    <textarea id="amenities" name="amenities" placeholder="WiFi&#10;Parking&#10;Kitchen">{{ is_array($amenitiesValue) ? implode(PHP_EOL, $amenitiesValue) : $amenitiesValue }}</textarea>
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
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="{{ route('owner.accommodations.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>
