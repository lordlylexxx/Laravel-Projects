@php
    use App\Helpers\FeatureHelper;
    $currentPlan = FeatureHelper::currentPlan();
    $remainingListings = FeatureHelper::remainingListings();
    $hasReachedLimit = FeatureHelper::hasReachedListingLimit();
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Accommodation - ImpaStay</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        :root {
            --green-dark: #1B5E20; --green-primary: #2E7D32; --green-medium: #43A047;
            --green-soft: #C8E6C9; --green-white: #E8F5E9;
            --red-light: #fee2e2; --red-dark: #991b1b;
            --yellow-light: #fef3c7; --yellow-dark: #b45309;
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

        /* Plan Status Alert */
        .plan-alert {
            background: var(--green-white);
            border-left: 4px solid var(--green-primary);
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .plan-alert-icon {
            font-size: 1.4rem;
            color: var(--green-primary);
        }

        .plan-alert-content h3 {
            margin: 0 0 4px 0;
            color: var(--green-dark);
            font-size: 1rem;
        }

        .plan-alert-content p {
            margin: 0;
            color: #4b5563;
            font-size: 0.9rem;
        }

        /* Limit Warning */
        .limit-warning {
            background: var(--yellow-light);
            border-left: 4px solid var(--yellow-dark);
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .limit-warning-icon {
            font-size: 1.2rem;
            color: var(--yellow-dark);
            margin-top: 2px;
            flex-shrink: 0;
        }

        .limit-warning-content h3 {
            margin: 0 0 6px 0;
            color: var(--yellow-dark);
            font-size: 1rem;
        }

        .limit-warning-content p {
            margin: 0 0 8px 0;
            color: #7c2d12;
            font-size: 0.9rem;
        }

        .upgrade-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--green-primary);
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .upgrade-btn:hover {
            background: var(--green-dark);
        }

        /* Disabled Form */
        .form-disabled {
            opacity: 0.6;
            pointer-events: none;
        }

        .grid { 
            display: grid; 
            gap: 16px; 
            grid-template-columns: repeat(2, 1fr); 
        }

        .full { grid-column: 1 / -1; }

        label { 
            display: block; 
            font-weight: 600; 
            margin-bottom: 8px;
            color: #374151;
        }

        input[type="text"],
        input[type="number"],
        input[type="email"],
        input[type="tel"],
        input[type="date"],
        select,
        textarea {
            width: 100%;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 10px 12px;
            font-size: 14px;
            font-family: inherit;
            transition: border-color 0.2s;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: var(--green-primary);
            box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1);
        }

        textarea { 
            min-height: 100px;
            resize: vertical;
        }

        .hint { 
            color: #6b7280; 
            font-size: 0.85rem; 
            margin-top: 4px; 
        }

        .error-list {
            background: var(--red-light);
            color: var(--red-dark);
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 12px 14px;
            margin-bottom: 20px;
        }

        .error-list strong {
            display: block;
            margin-bottom: 6px;
        }

        .error-list ul {
            margin: 0;
            padding-left: 20px;
        }

        .error-list li {
            margin-bottom: 4px;
        }

        .actions {
            margin-top: 24px;
            display: flex;
            gap: 12px;
            justify-content: flex-end;
        }

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
            transition: all 0.2s;
        }

        .btn-primary {
            background: var(--green-primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--green-dark);
            transform: translateY(-1px);
        }

        .btn-primary:disabled {
            background: #9ca3af;
            cursor: not-allowed;
            transform: none;
        }

        .btn-secondary {
            background: #e5e7eb;
            color: #111827;
        }

        .btn-secondary:hover {
            background: #d1d5db;
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--green-dark);
            margin: 24px 0 16px 0;
            padding-top: 16px;
            border-top: 1px solid #e5e7eb;
        }

        .field-group {
            margin-bottom: 4px;
        }

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
            <h1>
                <i class="fas fa-plus-circle"></i> Create New Accommodation
            </h1>
            <a href="{{ route('owner.accommodations.index') }}">
                <i class="fas fa-arrow-left"></i> Back to Listings
            </a>
        </div>

        <!-- Plan Status Alert -->
        <div class="plan-alert">
            <div class="plan-alert-icon">
                <i class="fas fa-crown"></i>
            </div>
            <div class="plan-alert-content">
                <h3>You're on the {{ $currentPlan['name'] ?? 'Basic Plan' }}</h3>
                <p>
                    You can create up to 
                    @if($currentPlan['max_listings'] === null)
                        <strong>unlimited</strong> properties
                    @else
                        <strong>{{ $currentPlan['max_listings'] }} properties</strong>
                        (@if($remainingListings === 0)
                            <strong class="text-warning">limit reached</strong>
                        @else
                            {{ $remainingListings }} remaining
                        @endif)
                    @endif
                </p>
            </div>
        </div>

        <!-- Limit Warning (if reached) -->
        @if($hasReachedLimit)
            <div class="limit-warning">
                <div class="limit-warning-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="limit-warning-content">
                    <h3>You've Reached Your Listing Limit</h3>
                    <p>Your current plan allows {{ $currentPlan['max_listings'] }} property listings. To add more properties, upgrade your plan.</p>
                    <a href="/" class="upgrade-btn">
                        <i class="fas fa-arrow-up"></i> Upgrade Your Plan
                    </a>
                </div>
            </div>

            <!-- Disabled Form -->
            <div class="form-disabled">
        @endif

        @if ($errors->any())
            <div class="error-list">
                <strong><i class="fas fa-exclamation-circle"></i> Please fix the following:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('owner.accommodations.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="section-title">
                <i class="fas fa-info-circle"></i> Property Information
            </div>

            <div class="grid">
                <div class="field-group">
                    <label for="name">Property Name *</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" placeholder="e.g., Beachfront Bungalow" required>
                    <div class="hint">Give your property a catchy name</div>
                </div>

                <div class="field-group">
                    <label for="type">Property Type *</label>
                    <select id="type" name="type" required>
                        <option value="">Select a type...</option>
                        <option value="traveller-inn" @selected(old('type') === 'traveller-inn')>Traveller Inn</option>
                        <option value="airbnb" @selected(old('type') === 'airbnb')>Airbnb</option>
                        <option value="daily-rental" @selected(old('type') === 'daily-rental')>Daily Rental</option>
                    </select>
                </div>

                <div class="full field-group">
                    <label for="description">Description *</label>
                    <textarea id="description" name="description" required placeholder="Describe your property, amenities, and what makes it special...">{{ old('description') }}</textarea>
                    <div class="hint">Write a compelling description to attract guests</div>
                </div>

                <div class="field-group">
                    <label for="address">Address *</label>
                    <input id="address" name="address" type="text" value="{{ old('address') }}" placeholder="Street address" required>
                </div>

                <div class="field-group">
                    <label for="barangay">Barangay *</label>
                    <input id="barangay" name="barangay" type="text" value="{{ old('barangay') }}" placeholder="e.g., Impasugong" required>
                </div>
            </div>

            <div class="section-title">
                <i class="fas fa-tag"></i> Pricing
            </div>

            <div class="grid">
                <div class="field-group">
                    <label for="price_per_night">Price per Night (₱) *</label>
                    <input id="price_per_night" name="price_per_night" type="number" min="0" step="0.01" value="{{ old('price_per_night') }}" placeholder="0.00" required>
                </div>

                <div class="field-group">
                    <label for="price_per_day">Price per Day (₱)</label>
                    <input id="price_per_day" name="price_per_day" type="number" min="0" step="0.01" value="{{ old('price_per_day') }}" placeholder="0.00">
                    <div class="hint">Optional: If different from nightly rate</div>
                </div>
            </div>

            <div class="section-title">
                <i class="fas fa-bed"></i> Details
            </div>

            <div class="grid">
                <div class="field-group">
                    <label for="bedrooms">Bedrooms</label>
                    <input id="bedrooms" name="bedrooms" type="number" min="0" value="{{ old('bedrooms') }}" placeholder="0">
                </div>

                <div class="field-group">
                    <label for="bathrooms">Bathrooms</label>
                    <input id="bathrooms" name="bathrooms" type="number" min="0" value="{{ old('bathrooms') }}" placeholder="0">
                </div>

                <div class="field-group">
                    <label for="max_guests">Max Guests</label>
                    <input id="max_guests" name="max_guests" type="number" min="1" value="{{ old('max_guests') }}" placeholder="1">
                </div>

                <div class="field-group">
                    <label for="primary_image">Featured Image</label>
                    <input id="primary_image" name="primary_image" type="file" accept="image/*">
                    <div class="hint">JPG, PNG or GIF (Max 5MB)</div>
                </div>
            </div>

            <div class="section-title">
                <i class="fas fa-star"></i> Amenities & Rules
            </div>

            <div class="full field-group">
                <label for="amenities">Amenities (comma-separated)</label>
                <textarea id="amenities" name="amenities" placeholder="e.g., WiFi, Air Conditioning, Kitchen, Parking...">{{ old('amenities') }}</textarea>
                <div class="hint">List amenities separated by commas</div>
            </div>

            <div class="full field-group">
                <label for="house_rules">House Rules</label>
                <textarea id="house_rules" name="house_rules" placeholder="e.g., No smoking, No loud noise after 10pm...">{{ old('house_rules') }}</textarea>
            </div>

            <div class="full field-group">
                <label for="check_in_instructions">Check-in Instructions</label>
                <textarea id="check_in_instructions" name="check_in_instructions" placeholder="e.g., Key is hidden under the mat...">{{ old('check_in_instructions') }}</textarea>
            </div>

            <div class="actions">
                <a href="{{ route('owner.accommodations.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary" @if($hasReachedLimit) disabled @endif>
                    <i class="fas fa-check"></i> Create Property
                </button>
            </div>

        </form>

        @if($hasReachedLimit)
            </div> <!-- End disabled form div -->
        @endif
    </div>
</body>
</html>
