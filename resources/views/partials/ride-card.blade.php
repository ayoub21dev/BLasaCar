@php
    $driver = $ride->user;
    $profile = $driver?->driverProfile;
    $initials = strtoupper(substr($driver?->first_name ?? 'B', 0, 1).substr($driver?->last_name ?? 'C', 0, 1));
@endphp

<article class="surface-soft overflow-hidden transition duration-200 hover:-translate-y-1 hover:shadow-[0_24px_60px_-38px_rgba(14,165,233,0.5)]">
    <a href="{{ route('rides.show', $ride) }}" class="block p-5 sm:p-6">
        <div class="flex items-start justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-gradient-to-br from-brand-500 to-brand-700 text-sm font-bold text-white shadow-lg shadow-brand-500/25">
                    {{ $initials }}
                </div>
                <div>
                    <p class="font-semibold text-slate-900">{{ $driver?->first_name }} {{ substr($driver?->last_name ?? '', 0, 1) }}.</p>
                    <div class="mt-1 flex items-center gap-2 text-sm text-slate-500">
                        <span class="inline-flex items-center gap-1">
                            <svg class="h-4 w-4 text-amber-400" viewBox="0 0 24 24" fill="currentColor">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                            </svg>
                            {{ number_format((float) ($profile?->avg_rating ?? 4.8), 1) }}
                        </span>
                        <span>&middot;</span>
                        <span>{{ $profile?->total_trips ?? 0 }} trips</span>
                    </div>
                </div>
            </div>

            @include('partials.status-chip', ['status' => $ride->status])
        </div>

        <div class="mt-6 grid gap-4 md:grid-cols-[minmax(0,1fr)_auto_minmax(0,1fr)] md:items-center">
            <div>
                <p class="text-2xl font-bold text-slate-900">{{ $ride->departure_time->format('H:i') }}</p>
                <p class="text-sm text-slate-500">{{ $ride->departureCity?->name }}</p>
            </div>

            <div class="flex items-center gap-3 text-slate-400">
                <div class="h-px flex-1 bg-slate-200"></div>
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 16H9m10 0h3v-3.15a1 1 0 0 0-.84-.99L16 11l-2.7-3.6a1 1 0 0 0-.8-.4H5.24a2 2 0 0 0-1.8 1.1l-.8 1.63A6 6 0 0 0 2 12.42V16h2" />
                </svg>
                <div class="h-px flex-1 bg-slate-200"></div>
            </div>

            <div class="text-left md:text-right">
                <p class="text-2xl font-bold text-slate-900">{{ $ride->arrivalCity?->name }}</p>
                <p class="text-sm text-slate-500">{{ $ride->departure_time->translatedFormat('D d M') }}</p>
            </div>
        </div>

        <div class="mt-6 flex flex-col gap-4 border-t border-slate-100 pt-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex flex-wrap items-center gap-3 text-sm text-slate-500">
                <span>{{ $ride->available_seats }} seats left</span>
                <span>&middot;</span>
                <span>{{ $ride->meeting_point }}</span>
            </div>

            <div class="flex items-center justify-between gap-4 sm:justify-end">
                <span class="text-2xl font-bold text-brand-700">{{ number_format((float) $ride->price_per_seat, 0) }} DH</span>
                <span class="brand-button text-sm">View ride</span>
            </div>
        </div>
    </a>
</article>
