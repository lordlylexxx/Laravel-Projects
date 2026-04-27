@php
    use App\Helpers\FeatureHelper;
    $currentPlan = FeatureHelper::currentPlan();
    $remainingListings = FeatureHelper::remainingListings();
    $hasReachedLimit = FeatureHelper::hasReachedListingLimit();
    $cannotCreate = ! ($canCreate ?? false);
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('partials.tenant-favicon')
    <title>Create Accommodation - ImpaStay</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind = {
            config: {
                corePlugins: {
                    preflight: false,
                },
            },
        };
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-green-50 via-lime-50 to-white text-gray-800">
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
        $amenitiesInput = old('amenities', []);
        if (is_string($amenitiesInput)) {
            $amenitiesInput = preg_split('/\r\n|\r|\n|,/', $amenitiesInput) ?: [];
        }
        $selectedAmenities = collect($amenitiesInput)
            ->map(fn ($item) => trim((string) $item))
            ->filter()
            ->values()
            ->all();
    @endphp

    <div class="mx-auto min-h-screen w-full max-w-[1800px] px-4 py-6 sm:px-6 lg:px-10">
        <div class="mb-6 rounded-2xl border border-green-100 bg-white/85 p-6 shadow-sm backdrop-blur-sm">
            <div class="flex flex-col gap-4 border-b border-green-100 pb-4 md:flex-row md:items-center md:justify-between">
                <h1 class="text-2xl font-bold text-green-900 sm:text-3xl">
                    <i class="fas fa-plus-circle mr-2 text-green-600"></i>
                    Create New Accommodation
                </h1>
                <a href="/owner/accommodations" class="inline-flex items-center gap-2 rounded-lg border border-green-200 px-4 py-2 text-sm font-semibold text-green-700 transition hover:border-green-400 hover:bg-green-50">
                    <i class="fas fa-arrow-left"></i> Back to Listings
                </a>
            </div>

            <div class="mt-4 flex items-start gap-3 rounded-xl border border-green-100 bg-green-50 p-4">
                <i class="fas fa-crown mt-0.5 text-green-700"></i>
                <div class="text-sm text-gray-700">
                    <h3 class="mb-1 text-base font-semibold text-green-900">You're on the {{ $currentPlan['name'] ?? 'Basic Plan' }}</h3>
                    <p>
                        You can create up to
                        @if($currentPlan['max_listings'] === null)
                            <strong>unlimited</strong> properties
                        @else
                            <strong>{{ $currentPlan['max_listings'] }} properties</strong>
                            (@if($remainingListings === 0)
                                <strong class="text-amber-700">limit reached</strong>
                            @else
                                {{ $remainingListings }} remaining
                            @endif)
                        @endif
                    </p>
                </div>
            </div>

            @if($cannotCreate)
                <div class="mt-4 flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 p-4">
                    <i class="fas fa-exclamation-triangle mt-0.5 text-amber-700"></i>
                    <div class="text-sm text-amber-900">
                        @if($hasReachedLimit && ($currentPlan['max_listings'] ?? null) !== null)
                            <h3 class="mb-1 text-base font-semibold">You've Reached Your Listing Limit</h3>
                            <p class="mb-3">Your current plan allows {{ $currentPlan['max_listings'] }} property listings (Basic: 3, Standard: 10, Premium: unlimited). Upgrade to add more.</p>
                        @else
                            <h3 class="mb-1 text-base font-semibold">Cannot Add Listings</h3>
                            <p class="mb-3">Your subscription may be inactive or your plan does not allow new listings. Contact support or upgrade your plan.</p>
                        @endif
                        <a href="{{ route('owner.updates.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-green-700 px-4 py-2 font-semibold text-white transition hover:bg-green-800">
                            <i class="fas fa-arrow-up"></i> View plan &amp; updates
                        </a>
                    </div>
                </div>
            @endif
        </div>

        @if ($errors->any())
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-red-900 shadow-sm">
                <strong class="mb-2 block text-sm font-semibold"><i class="fas fa-exclamation-circle mr-1"></i> Please fix the following:</strong>
                <ul class="list-disc space-y-1 pl-5 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="/owner/accommodations" method="POST" enctype="multipart/form-data" class="@if($cannotCreate) pointer-events-none opacity-60 @endif">
            @csrf

            <div class="grid gap-6 xl:grid-cols-2">
                <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <h2 class="mb-5 text-lg font-bold text-green-900"><i class="fas fa-info-circle mr-2 text-green-700"></i> Property Information</h2>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label for="name" class="mb-2 block text-sm font-semibold text-gray-700">Property Name *</label>
                            <input id="name" name="name" type="text" value="{{ old('name') }}" placeholder="e.g., Beachfront Bungalow" required class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-green-600 focus:outline-none focus:ring-2 focus:ring-green-100">
                            <p class="mt-1 text-xs text-gray-500">Give your property a catchy name</p>
                        </div>

                        <div class="sm:col-span-2">
                            <label for="type" class="mb-2 block text-sm font-semibold text-gray-700">Property Type *</label>
                            <select id="type" name="type" required class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-green-600 focus:outline-none focus:ring-2 focus:ring-green-100">
                                <option value="">Select a type...</option>
                                <option value="traveller-inn" @selected(old('type') === 'traveller-inn')>Traveller Inn</option>
                                <option value="airbnb" @selected(old('type') === 'airbnb')>Airbnb</option>
                                <option value="daily-rental" @selected(old('type') === 'daily-rental')>Daily Rental</option>
                            </select>
                        </div>

                        <div class="sm:col-span-2">
                            <label for="description" class="mb-2 block text-sm font-semibold text-gray-700">Description *</label>
                            <textarea id="description" name="description" required placeholder="Describe your property, amenities, and what makes it special..." class="min-h-[120px] w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-green-600 focus:outline-none focus:ring-2 focus:ring-green-100">{{ old('description') }}</textarea>
                            <p class="mt-1 text-xs text-gray-500">Write a compelling description to attract guests</p>
                        </div>

                        <div>
                            <label for="address" class="mb-2 block text-sm font-semibold text-gray-700">Address *</label>
                            <input id="address" name="address" type="text" value="{{ old('address') }}" placeholder="Street address" required class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-green-600 focus:outline-none focus:ring-2 focus:ring-green-100">
                        </div>

                        <div>
                            <label for="barangay" class="mb-2 block text-sm font-semibold text-gray-700">Barangay *</label>
                            <input id="barangay" name="barangay" type="text" value="{{ old('barangay') }}" placeholder="e.g., Impasugong" required class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-green-600 focus:outline-none focus:ring-2 focus:ring-green-100">
                        </div>
                    </div>
                </section>

                <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <h2 class="mb-5 text-lg font-bold text-green-900"><i class="fas fa-tag mr-2 text-green-700"></i> Pricing & Details</h2>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="price_per_night" class="mb-2 block text-sm font-semibold text-gray-700">Price per Night (PHP) *</label>
                            <input id="price_per_night" name="price_per_night" type="number" min="0" step="0.01" value="{{ old('price_per_night') }}" placeholder="0.00" required class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-green-600 focus:outline-none focus:ring-2 focus:ring-green-100">
                        </div>
                        <div>
                            <label for="price_per_day" class="mb-2 block text-sm font-semibold text-gray-700">Price per Day (PHP)</label>
                            <input id="price_per_day" name="price_per_day" type="number" min="0" step="0.01" value="{{ old('price_per_day') }}" placeholder="0.00" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-green-600 focus:outline-none focus:ring-2 focus:ring-green-100">
                            <p class="mt-1 text-xs text-gray-500">Optional: If different from nightly rate</p>
                        </div>

                        <div>
                            <label for="bedrooms" class="mb-2 block text-sm font-semibold text-gray-700">Bedrooms</label>
                            <input id="bedrooms" name="bedrooms" type="number" min="0" value="{{ old('bedrooms') }}" placeholder="0" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-green-600 focus:outline-none focus:ring-2 focus:ring-green-100">
                        </div>
                        <div>
                            <label for="bathrooms" class="mb-2 block text-sm font-semibold text-gray-700">Bathrooms</label>
                            <input id="bathrooms" name="bathrooms" type="number" min="0" value="{{ old('bathrooms') }}" placeholder="0" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-green-600 focus:outline-none focus:ring-2 focus:ring-green-100">
                        </div>

                        <div class="sm:col-span-2">
                            <label for="max_guests" class="mb-2 block text-sm font-semibold text-gray-700">Max Guests *</label>
                            <input id="max_guests" name="max_guests" type="number" min="1" value="{{ old('max_guests', 1) }}" placeholder="1" required class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-green-600 focus:outline-none focus:ring-2 focus:ring-green-100">
                        </div>

                        <div class="sm:col-span-2">
                            <label for="primary_image" class="mb-2 block text-sm font-semibold text-gray-700">Featured Image</label>
                            <input id="primary_image" name="primary_image" type="file" accept="image/*" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm">
                            <p class="mt-1 text-xs text-gray-500">JPG, PNG or GIF (Max 5MB)</p>
                        </div>
                        <div class="sm:col-span-2">
                            <label for="images" class="mb-2 block text-sm font-semibold text-gray-700">Room Photos (Multiple)</label>
                            <input id="images" name="images[]" type="file" accept="image/*" multiple class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm">
                            <p class="mt-1 text-xs text-gray-500">Upload up to 10 photos (JPG, PNG, or WebP, max 5MB each).</p>
                        </div>
                    </div>
                </section>

                <section class="xl:col-span-2 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <h2 class="mb-5 text-lg font-bold text-green-900"><i class="fas fa-star mr-2 text-green-700"></i> Amenities & Rules</h2>
                    <div class="grid gap-6 lg:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-gray-700">Basic Amenities</label>
                            <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                                @foreach($basicAmenities as $amenity)
                                    <label class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm font-medium text-gray-700">
                                        <input type="checkbox" name="amenities[]" value="{{ $amenity }}" {{ in_array($amenity, $selectedAmenities, true) ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-green-700 focus:ring-green-600">
                                        <span>{{ $amenity }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <p class="mt-2 text-xs text-gray-500">Select all amenities available in this property.</p>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label for="house_rules" class="mb-2 block text-sm font-semibold text-gray-700">House Rules</label>
                                <textarea id="house_rules" name="house_rules" placeholder="e.g., No smoking, No loud noise after 10pm..." class="min-h-[110px] w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-green-600 focus:outline-none focus:ring-2 focus:ring-green-100">{{ old('house_rules') }}</textarea>
                            </div>
                            <div>
                                <label for="check_in_instructions" class="mb-2 block text-sm font-semibold text-gray-700">Check-in Instructions</label>
                                <textarea id="check_in_instructions" name="check_in_instructions" placeholder="e.g., Key is hidden under the mat..." class="min-h-[110px] w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-green-600 focus:outline-none focus:ring-2 focus:ring-green-100">{{ old('check_in_instructions') }}</textarea>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <div class="sticky bottom-0 mt-6 rounded-2xl border border-green-100 bg-white/95 p-4 shadow-md backdrop-blur-sm">
                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-end">
                    <a href="/owner/accommodations" class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-gray-100 px-5 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-200">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-green-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-green-800 disabled:cursor-not-allowed disabled:bg-gray-400" @if($cannotCreate) disabled @endif>
                        <i class="fas fa-check"></i> Create Property
                    </button>
                </div>
            </div>
        </form>
    </div>
</body>
</html>
