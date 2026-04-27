@php
    $driver = $ride->user;
    $profile = $driver?->driverProfile;
    $isToday = $ride->departure_time->isToday();
    $isTomorrow = $ride->departure_time->isTomorrow();
    $dateLabel = $isToday ? 'Today' : ($isTomorrow ? 'Tomorrow' : $ride->departure_time->format('D, M d'));
@endphp

<article class="surface-soft transition duration-200 hover:shadow-xl hover:shadow-slate-200/50">
    <a href="{{ route('rides.show', $ride) }}" class="block p-6">
        {{-- Driver Header --}}
        <div class="flex items-center gap-3">
            <div class="relative">
                @if($driver?->profile_photo_path)
                    <img src="{{ Storage::url($driver->profile_photo_path) }}" alt="{{ $driver->name }}" class="h-12 w-12 rounded-full object-cover border-2 border-white shadow-sm">
                @else
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-sm font-bold text-slate-600 border-2 border-white shadow-sm">
                        {{ strtoupper(substr($driver?->first_name ?? 'B', 0, 1)) }}
                    </div>
                @endif
                <div class="absolute -bottom-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-white shadow-sm">
                    <svg class="h-4 w-4 text-emerald-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>
            <div>
                <p class="font-bold text-slate-900">{{ $driver?->first_name }} {{ substr($driver?->last_name ?? '', 0, 1) }}.</p>
                <div class="flex items-center gap-1.5 text-xs text-slate-500 font-medium">
                    <svg class="h-3.5 w-3.5 text-amber-400" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    <span>{{ number_format((float) ($profile?->avg_rating ?? 4.8), 1) }}</span>
                    <span class="text-slate-300">({{ $profile?->total_trips ?? 24 }} trips)</span>
                </div>
            </div>
        </div>

        {{-- Route Timeline --}}
        <div class="mt-8 flex items-center justify-between gap-4 max-sm:flex-col max-sm:items-stretch">
            <div class="flex-shrink-0">
                <p class="text-xl font-black text-slate-900">{{ $ride->departure_time->format('H:i') }}</p>
                <p class="text-sm font-medium text-slate-500">{{ $ride->departureCity?->name }}</p>
            </div>

            <div class="flex flex-1 items-center gap-1 max-sm:my-1">
                <div class="h-1.5 w-1.5 rounded-full bg-brand-500 shadow-[0_0_8px_rgba(14,165,233,0.5)]"></div>
                <div class="relative flex flex-1 items-center justify-center">
                    <div class="h-px w-full border-t border-dashed border-slate-300"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="bg-white px-2">
                            <svg class="h-5 w-5 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M14 16H9m10 0h3v-3.15a1 1 0 0 0-.84-.99L16 11l-2.7-3.6a1 1 0 0 0-.8-.4H5.24a2 2 0 0 0-1.8 1.1l-.8 1.63A6 6 0 0 0 2 12.42V16h2" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="h-1.5 w-1.5 rounded-full bg-brand-500 shadow-[0_0_8px_rgba(14,165,233,0.5)]"></div>
            </div>

            <div class="flex-shrink-0 text-right max-sm:text-left">
                <p class="text-xl font-black text-slate-900">{{ $ride->departure_time->addHours(2)->addMinutes(30)->format('H:i') }}</p>
                <p class="text-sm font-medium text-slate-500">{{ $ride->arrivalCity?->name }}</p>
            </div>
        </div>

        {{-- Footer Details --}}
        <div class="mt-10 flex items-center justify-between gap-5 border-t border-slate-50 pt-6 max-sm:flex-col max-sm:items-start">
            <div class="flex flex-wrap items-center gap-3 text-xs font-semibold uppercase tracking-wider text-slate-400">
                <div class="flex items-center gap-1.5">
                    <svg class="h-4 w-4 opacity-70" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                    </svg>
                    <span>{{ $ride->available_seats }} {{ \Illuminate\Support\Str::plural('seat', $ride->available_seats) }} left</span>
                </div>
                <span>&bull;</span>
                <span>{{ $dateLabel }}</span>
            </div>

            <div class="flex w-full items-center justify-between gap-5 sm:w-auto">
                <span class="text-2xl font-black text-brand-600">{{ number_format((float) $ride->price_per_seat, 0) }} DH</span>
                <span class="inline-flex items-center justify-center rounded-xl bg-brand-600 px-6 py-2.5 text-sm font-bold text-white transition hover:bg-brand-700 hover:shadow-lg hover:shadow-brand-200">
                    Book
                </span>
            </div>
        </div>
    </a>
</article>
