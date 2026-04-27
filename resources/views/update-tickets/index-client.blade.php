<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('partials.tenant-favicon')
    <title>Support - ImpaStay</title>
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
            @include('partials.tenant-theme-css-vars')
            --gray-200: #E5E7EB;
            --gray-500: #6B7280;
            --gray-600: #4B5563;
            --gray-700: #374151;
            --gray-800: #1F2937;
        }
        @include('client.partials.top-navbar-styles')
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-green-50 via-lime-50 to-white text-gray-800">
    @include('client.partials.top-navbar', ['active' => 'update-tickets'])

    <main class="mx-auto min-h-screen w-full max-w-[1800px] px-4 pb-10 sm:px-6 lg:px-10" style="padding-top: calc(var(--client-nav-offset, 108px) + 24px);">
        @if(session('success'))
            <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-6 rounded-2xl border border-green-100 bg-white/85 p-6 shadow-sm backdrop-blur-sm">
            <h1 class="mb-2 text-2xl font-bold text-green-900 sm:text-3xl">
                <i class="fas fa-life-ring mr-2 text-green-700"></i> Support
            </h1>
            <p class="text-sm text-gray-600 sm:text-base">Submit issues about system updates or downloads. Central admin will review your ticket.</p>
        </div>

        <div class="grid gap-6 xl:grid-cols-5">
            <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm xl:col-span-2">
                <h2 class="mb-4 text-lg font-bold text-gray-800">New Ticket</h2>
                <form method="POST" action="/update-tickets" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div>
                        <label for="subject" class="mb-2 block text-sm font-semibold text-gray-700">Subject</label>
                        <input id="subject" name="subject" type="text" value="{{ old('subject') }}" required maxlength="255" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-green-600 focus:outline-none focus:ring-2 focus:ring-green-100">
                    </div>

                    <div>
                        <label for="body" class="mb-2 block text-sm font-semibold text-gray-700">Details</label>
                        <textarea id="body" name="body" rows="6" required maxlength="10000" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-green-600 focus:outline-none focus:ring-2 focus:ring-green-100">{{ old('body') }}</textarea>
                    </div>

                    <div>
                        <label for="attachment" class="mb-2 block text-sm font-semibold text-gray-700">Photo attachment <span class="font-normal text-gray-500">(optional, JPG/PNG/WEBP up to 5MB)</span></label>
                        <input id="attachment" name="attachment" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm">
                    </div>

                    @if ($errors->any())
                        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-800">{{ $errors->first() }}</div>
                    @endif

                    <div class="pt-2">
                        <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-green-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-green-800">
                            <i class="fas fa-paper-plane"></i> Submit
                        </button>
                    </div>
                </form>
            </section>

            <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm xl:col-span-3">
                <h2 class="mb-4 text-lg font-bold text-gray-800">Your Tickets</h2>

                <div class="overflow-x-auto rounded-xl border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-green-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Subject</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($tickets as $ticket)
                        <tr>
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600">{{ $ticket->created_at?->format('M j, Y') }}</td>
                            <td class="px-4 py-3 text-sm text-gray-800">{{ \Illuminate\Support\Str::limit($ticket->subject, 50) }}</td>
                            <td class="px-4 py-3">
                                @if($ticket->status === \App\Models\UpdateTicket::STATUS_RESOLVED)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700"><i class="fas fa-check"></i> Resolved</span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-800"><i class="fas fa-inbox"></i> Open</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <a href="/update-tickets/{{ $ticket->id }}" class="inline-flex items-center rounded-lg bg-green-700 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-green-800">View</a>
                            </td>
                        </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-6 text-center text-sm text-gray-500">No tickets yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">{{ $tickets->links() }}</div>
            </section>
        </div>
    </main>
</body>
</html>
