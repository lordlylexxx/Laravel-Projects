<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('partials.tenant-favicon')
    <title>Client Dashboard - ImpaStay</title>
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
    <style>
        :root {
            --green-dark: #1B5E20; --green-primary: #2E7D32; --green-medium: #43A047;
            --green-soft: #C8E6C9; --green-white: #E8F5E9; --white: #FFFFFF;
            --gray-200: #E5E7EB; --gray-300: #D1D5DB; --gray-500: #6B7280;
            --gray-600: #4B5563; --gray-700: #374151; --gray-800: #1F2937;
        }

        @include('client.partials.top-navbar-styles')
        .availability-controls {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 14px;
        }
        .availability-select {
            min-width: 240px;
            padding: 10px 12px;
            border: 1px solid var(--gray-300);
            border-radius: 10px;
            color: var(--gray-800);
            background: #fff;
        }
        .month-nav-btn {
            border: 1px solid var(--green-soft);
            background: var(--green-white);
            color: var(--green-dark);
            border-radius: 9px;
            width: 34px;
            height: 34px;
            cursor: pointer;
            font-weight: 700;
        }
        .availability-month-label {
            min-width: 150px;
            text-align: center;
            color: var(--green-dark);
            font-weight: 700;
        }
        .availability-grid {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: 6px;
        }
        .availability-dow,
        .availability-day {
            border-radius: 8px;
            text-align: center;
            padding: 9px 4px;
            font-size: 0.82rem;
        }
        .availability-dow {
            background: var(--green-white);
            color: var(--green-dark);
            font-weight: 700;
        }
        .availability-day {
            background: #fff;
            border: 1px solid var(--gray-200);
            color: var(--gray-700);
        }
        .availability-day.empty {
            background: transparent;
            border-color: transparent;
        }
        .availability-day.blocked {
            background: #fee2e2;
            border-color: #fecaca;
            color: #991b1b;
            font-weight: 700;
        }
        .availability-day.today {
            border-color: var(--green-primary);
            box-shadow: inset 0 0 0 1px var(--green-primary);
            font-weight: 700;
        }
        .availability-legend {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
            margin-top: 12px;
            color: var(--gray-600);
            font-size: 0.8rem;
        }
        .availability-dot {
            width: 10px;
            height: 10px;
            border-radius: 999px;
            display: inline-block;
            margin-right: 5px;
        }
        .availability-dot.available { background: #bbf7d0; }
        .availability-dot.blocked { background: #fecaca; }
        .snapshot-meta { color: var(--gray-500); font-size: 0.82rem; }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-green-50 via-lime-50 to-white text-gray-800">
    <!-- Navigation -->
    @include('client.partials.top-navbar', ['active' => 'dashboard'])
    
    <!-- Main Content -->
    <div class="min-h-screen">
        <!-- Hero Section -->
        <section class="relative overflow-hidden bg-gradient-to-br from-green-900 to-green-700 px-4 pb-12 pt-40 text-white sm:px-8 lg:px-12">
            <div class="absolute inset-0 bg-[url('/COMMUNAL.jpg')] bg-cover bg-center opacity-10"></div>
            <div class="relative mx-auto w-full max-w-[1800px] text-center">
                <div class="mb-3 flex min-h-[130px] flex-wrap items-center justify-center gap-4 sm:gap-6">
                    <img src="{{ asset('Love Impasugong.png') }}" alt="Love Impasugong" width="140" height="140" class="h-24 w-24 object-contain drop-shadow-[0_4px_12px_rgba(0,0,0,0.4)] sm:h-32 sm:w-32">
                    <img src="{{ asset('SYSTEMLOGO.png') }}" alt="ImpaStay Logo" width="140" height="140" class="h-24 w-24 object-contain drop-shadow-[0_4px_12px_rgba(0,0,0,0.4)] sm:h-32 sm:w-32">
                    <img src="{{ asset('Lgu Socmed Template-02.png') }}" alt="LGU Impasugong" width="140" height="140" class="h-24 w-24 object-contain drop-shadow-[0_4px_12px_rgba(0,0,0,0.4)] sm:h-32 sm:w-32">
                </div>
                <h1 class="mb-2 text-3xl font-bold sm:text-4xl"><i class="fas fa-home mr-2" aria-hidden="true"></i>Find Your Perfect Stay</h1>
                <p class="text-sm text-green-50 sm:text-lg">Discover traveller-inns, Airbnb stays, and daily rentals in Impasugong</p>
            </div>
        </section>
        
        <!-- Property Categories -->
        <section class="mx-auto w-full max-w-[1800px] px-4 py-8 sm:px-6 lg:px-10">
            @if($canManageOwnStays)
            <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-2xl font-bold text-green-900"><i class="fas fa-chart-line mr-2"></i>My Booking Snapshot</h2>
                <a href="{{ route('bookings.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-green-700 transition hover:text-green-900"><i class="fas fa-arrow-right"></i> Manage Bookings</a>
            </div>

            <div class="mb-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-xl border border-green-100 bg-white p-4 shadow-sm">
                    <h4 class="mb-2 text-sm font-semibold text-green-900"><i class="fas fa-suitcase-rolling mr-2"></i> Upcoming Trips</h4>
                    <div class="mb-1 text-3xl font-bold text-green-700">{{ $upcomingBookingsCount ?? 0 }}</div>
                    <div class="snapshot-meta">Active stays and confirmed arrivals</div>
                </div>

                <div class="rounded-xl border border-green-100 bg-white p-4 shadow-sm">
                    <h4 class="mb-2 text-sm font-semibold text-green-900"><i class="fas fa-hourglass-half mr-2"></i> Pending Requests</h4>
                    <div class="mb-1 text-3xl font-bold text-green-700">{{ $pendingBookingsCount ?? 0 }}</div>
                    <div class="snapshot-meta">Waiting for owner confirmation</div>
                </div>

                <div class="rounded-xl border border-green-100 bg-white p-4 shadow-sm">
                    <h4 class="mb-2 text-sm font-semibold text-green-900"><i class="fas fa-wallet mr-2"></i> Year-to-Date Spend</h4>
                    <div class="mb-1 text-3xl font-bold text-green-700">₱{{ number_format($ytdSpend ?? 0, 0) }}</div>
                    <div class="snapshot-meta">Paid and completed bookings this year</div>
                </div>

                <div class="rounded-xl border border-green-100 border-l-4 bg-white p-4 shadow-sm">
                    <h4 class="mb-2 text-sm font-semibold text-green-900"><i class="fas fa-plane-departure mr-2"></i> Next Trip</h4>
                    @if($nextUpcomingBooking)
                        <div class="mb-1 text-lg font-bold text-green-800">{{ $nextUpcomingBooking->accommodation->name ?? 'Accommodation' }}</div>
                        <div class="snapshot-meta">
                            {{ optional($nextUpcomingBooking->check_in_date)->format('M d, Y') }} - {{ optional($nextUpcomingBooking->check_out_date)->format('M d, Y') }}
                        </div>
                    @else
                        <div class="text-sm text-gray-600">No upcoming booking yet. Explore available stays.</div>
                    @endif
                </div>
            </div>
            @else
            <div class="mb-4 flex items-center gap-2">
                <h2 class="text-2xl font-bold text-green-900"><i class="fas fa-chart-line mr-2"></i>Bookings</h2>
            </div>
            <p class="mb-6 max-w-2xl text-sm text-gray-600 sm:text-base">Your account does not have permission to create or manage bookings on this site. Contact the business if you need this enabled.</p>
            @endif

            <div class="mb-4 flex items-center gap-2">
                <h2 class="text-2xl font-bold text-green-900"><i class="fas fa-calendar-days mr-2"></i>Room Availability Calendar</h2>
            </div>
            <div class="mb-6 rounded-2xl border border-green-100 bg-white p-5 shadow-sm">
                @if(($availabilityAccommodations ?? collect())->isNotEmpty())
                    <div class="availability-controls">
                        <select id="clientAvailabilityAccommodation" class="min-w-[240px] rounded-lg border border-gray-300 px-3 py-2.5 text-sm text-gray-800 focus:border-green-700 focus:outline-none focus:ring-2 focus:ring-green-100" aria-label="Select room for availability calendar">
                            @foreach($availabilityAccommodations as $accommodation)
                                <option value="{{ $accommodation->id }}">{{ $accommodation->name }} ({{ str_replace('-', ' ', $accommodation->type) }})</option>
                            @endforeach
                        </select>
                        <button type="button" class="h-9 w-9 rounded-lg border border-green-200 bg-green-50 font-bold text-green-900 transition hover:bg-green-100" id="clientAvailabilityPrev" aria-label="Previous month">&lt;</button>
                        <div class="min-w-[150px] text-center font-bold text-green-900" id="clientAvailabilityMonthLabel">Month</div>
                        <button type="button" class="h-9 w-9 rounded-lg border border-green-200 bg-green-50 font-bold text-green-900 transition hover:bg-green-100" id="clientAvailabilityNext" aria-label="Next month">&gt;</button>
                    </div>
                    <div class="availability-grid" id="clientAvailabilityGrid"></div>
                    <div class="availability-legend">
                        <span><span class="availability-dot available"></span>Available</span>
                        <span><span class="availability-dot blocked"></span>Booked / Pending</span>
                    </div>
                @else
                    <p class="snapshot-meta">No rooms are currently listed for this tenant.</p>
                @endif
            </div>

        </section>
        
        <!-- Footer -->
        <footer class="bg-green-900 px-4 py-8 text-center text-white sm:px-6 lg:px-10">
            <p class="text-sm text-green-100"><i class="fas fa-copyright"></i> 2024 ImpaStay. Impasugong Accommodations Platform.</p>
        </footer>
    </div>
    
    <script>
        (function () {
            const selectEl = document.getElementById('clientAvailabilityAccommodation');
            const gridEl = document.getElementById('clientAvailabilityGrid');
            const monthLabelEl = document.getElementById('clientAvailabilityMonthLabel');
            const prevBtn = document.getElementById('clientAvailabilityPrev');
            const nextBtn = document.getElementById('clientAvailabilityNext');

            if (!selectEl || !gridEl || !monthLabelEl || !prevBtn || !nextBtn) {
                return;
            }

            const eventsByAccommodation = @json($availabilityEventsByAccommodation ?? []);
            const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
            let currentMonth = new Date();
            currentMonth = new Date(currentMonth.getFullYear(), currentMonth.getMonth(), 1);

            const toDateKey = (date) => {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');

                return `${year}-${month}-${day}`;
            };

            const blockedSetForAccommodation = (accommodationId) => {
                const rows = eventsByAccommodation[String(accommodationId)] || eventsByAccommodation[accommodationId] || [];
                const blocked = new Set();

                rows.forEach((row) => {
                    if (!row.start || !row.end) {
                        return;
                    }

                    const start = new Date(`${row.start}T00:00:00`);
                    const end = new Date(`${row.end}T00:00:00`);

                    if (Number.isNaN(start.getTime()) || Number.isNaN(end.getTime())) {
                        return;
                    }

                    for (let cursor = new Date(start); cursor <= end; cursor.setDate(cursor.getDate() + 1)) {
                        blocked.add(toDateKey(cursor));
                    }
                });

                return blocked;
            };

            const renderCalendar = () => {
                const selectedAccommodationId = selectEl.value;
                const blockedSet = blockedSetForAccommodation(selectedAccommodationId);
                const year = currentMonth.getFullYear();
                const month = currentMonth.getMonth();
                const monthStart = new Date(year, month, 1);
                const monthEnd = new Date(year, month + 1, 0);
                const today = toDateKey(new Date());

                monthLabelEl.textContent = monthStart.toLocaleDateString(undefined, {
                    month: 'long',
                    year: 'numeric',
                });

                const cells = [];
                dayNames.forEach((name) => {
                    cells.push(`<div class="availability-dow">${name}</div>`);
                });

                for (let i = 0; i < monthStart.getDay(); i++) {
                    cells.push('<div class="availability-day empty"></div>');
                }

                for (let day = 1; day <= monthEnd.getDate(); day++) {
                    const date = new Date(year, month, day);
                    const key = toDateKey(date);
                    const isBlocked = blockedSet.has(key);
                    const isToday = key === today;

                    const classes = ['availability-day'];
                    if (isBlocked) classes.push('blocked');
                    if (isToday) classes.push('today');

                    cells.push(`<div class="${classes.join(' ')}" title="${isBlocked ? 'Not available' : 'Available'}">${day}</div>`);
                }

                gridEl.innerHTML = cells.join('');
            };

            selectEl.addEventListener('change', renderCalendar);
            prevBtn.addEventListener('click', () => {
                currentMonth = new Date(currentMonth.getFullYear(), currentMonth.getMonth() - 1, 1);
                renderCalendar();
            });
            nextBtn.addEventListener('click', () => {
                currentMonth = new Date(currentMonth.getFullYear(), currentMonth.getMonth() + 1, 1);
                renderCalendar();
            });

            renderCalendar();
        })();

        // Simple favorite toggle
        document.querySelectorAll('.property-favorite').forEach(btn => {
            btn.addEventListener('click', function() {
                const icon = this.querySelector('i');
                if (icon.classList.contains('far')) {
                    icon.classList.remove('far');
                    icon.classList.add('fas');
                    this.style.color = '#dc3545';
                } else {
                    icon.classList.remove('fas');
                    icon.classList.add('far');
                    this.style.color = '';
                }
            });
        });
    </script>
</body>
</html>
