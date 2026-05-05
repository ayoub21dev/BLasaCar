<div class="dashboard-panel">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.18em] text-brand-600">Rides</p>
            <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-950">Ride activity</h1>
            <p class="mt-2 text-sm text-slate-500">{{ $metrics['completed_rides'] }} completed &middot; {{ $metrics['cancelled_rides'] }} cancelled</p>
        </div>
    </div>

    <div class="mt-6 grid gap-4 lg:grid-cols-2">
        @foreach ($rides as $ride)
            <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-5">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="font-semibold text-slate-900">{{ $ride->departureCity?->name }} &rarr; {{ $ride->arrivalCity?->name }}</p>
                        <p class="mt-1 text-sm text-slate-500">{{ $ride->departure_time->format('d M Y \a\t H:i') }}</p>
                    </div>
                    @include('partials.status-chip', ['status' => $ride->status])
                </div>
                <div class="mt-4 text-sm text-slate-600">
                    Driver: {{ $ride->user?->first_name }} {{ $ride->user?->last_name }}
                </div>
            </div>
        @endforeach
    </div>
</div>
