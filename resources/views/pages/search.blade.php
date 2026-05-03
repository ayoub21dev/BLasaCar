@php($title = 'Find a route that fits your next trip.')

@extends('layouts.app')

@section('content')
    <section class="py-12">
        <div class="shell page-enter">
            <div class="bg-white rounded-[3.5rem] p-8 sm:p-12 lg:p-16 relative overflow-hidden shadow-sm border border-slate-100">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <div class="inline-flex items-center gap-2 rounded-full bg-brand-100 px-4 py-2 text-[12px] font-black uppercase tracking-widest text-brand-600 mb-6">
                            Search Rides
                        </div>
                        <h1 class="text-[3rem] sm:text-[4rem] font-black text-slate-900 leading-[0.95] tracking-tight">
                            Find a route for <br> your next <span class="italic font-serif text-brand-500">trip</span>.
                        </h1>
                    </div>
                    <p class="text-lg font-medium text-slate-500 bg-slate-50 px-6 py-3 rounded-full">
                        {{ $rides->count() }} ride{{ $rides->count() === 1 ? '' : 's' }} available
                    </p>
                </div>

                <form method="GET" action="{{ route('rides.search') }}" class="mt-6 grid gap-3 lg:grid-cols-[1fr_1fr_1fr_150px_160px]">
                    <label class="input-shell">
                        <svg class="h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                            <circle cx="12" cy="10" r="3" />
                        </svg>
                        <select name="departure_city_id" class="w-full bg-transparent text-sm font-medium text-slate-700 outline-none">
                            <option value="">Leaving from</option>
                            @foreach ($cities as $city)
                                <option value="{{ $city->id }}" @selected(($filters['departure_city_id'] ?? null) == $city->id)>{{ $city->name }}</option>
                            @endforeach
                        </select>
                    </label>

                    <label class="input-shell">
                        <svg class="h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                            <circle cx="12" cy="10" r="3" />
                        </svg>
                        <select name="arrival_city_id" class="w-full bg-transparent text-sm font-medium text-slate-700 outline-none">
                            <option value="">Going to</option>
                            @foreach ($cities as $city)
                                <option value="{{ $city->id }}" @selected(($filters['arrival_city_id'] ?? null) == $city->id)>{{ $city->name }}</option>
                            @endforeach
                        </select>
                    </label>

                    <label class="input-shell cursor-pointer">
                        <svg class="h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                            <line x1="16" x2="16" y1="2" y2="6" />
                            <line x1="8" x2="8" y1="2" y2="6" />
                            <line x1="3" x2="21" y1="10" y2="10" />
                        </svg>
                        <input type="date" name="departure_date" value="{{ $filters['departure_date'] ?? '' }}" onclick="this.showPicker()" class="date-input-clean w-full bg-transparent text-sm font-medium text-slate-700 outline-none cursor-pointer">
                    </label>

                    <label class="input-shell">
                        <svg class="h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                            <circle cx="12" cy="7" r="4" />
                        </svg>
                        <select name="seats" class="w-full bg-transparent text-sm font-medium text-slate-700 outline-none">
                            @foreach ([1, 2, 3, 4] as $seatCount)
                                <option value="{{ $seatCount }}" @selected(($filters['seats'] ?? 1) == $seatCount)>{{ $seatCount }} {{ \Illuminate\Support\Str::plural('seat', $seatCount) }}</option>
                            @endforeach
                        </select>
                    </label>

                    <button type="submit" class="brand-button rounded-[1.4rem]">
                        Update search
                    </button>
                </form>
            </div>

            <div class="mt-10 grid gap-8 xl:grid-cols-[minmax(0,1fr)_320px]">
                <div class="space-y-6">
                    @forelse ($rides as $ride)
                        @include('partials.ride-card', ['ride' => $ride])
                    @empty
                        <div class="surface-soft p-10 text-center">
                            <h2 class="text-2xl font-bold text-slate-900">No rides matched those filters.</h2>
                            <p class="mx-auto mt-3 max-w-md text-slate-500">
                                Try changing the departure city, destination, date, or seat count. The public service layer is already wired to filter only bookable trips.
                            </p>
                        </div>
                    @endforelse
                </div>

                <aside class="space-y-6">
                    <div class="surface-soft p-6">
                        <p class="text-sm font-semibold uppercase tracking-[0.18em] text-brand-600">Why this search works</p>
                        <ul class="mt-4 space-y-4 text-sm leading-6 text-slate-600">
                            <li>Only scheduled rides with available seats are shown.</li>
                            <li>Suspended accounts are excluded from booking results.</li>
                            <li>Date and minimum-seat filters are applied against real seeded rides.</li>
                        </ul>
                    </div>

                    <div class="surface-soft p-6">
                        <p class="text-sm font-semibold uppercase tracking-[0.18em] text-brand-600">Next pages</p>
                        <div class="mt-4 space-y-3">
                            <a href="{{ route('rides.publish') }}" class="block rounded-2xl border border-slate-200 px-4 py-3 text-sm font-medium text-slate-700 transition hover:border-brand-200 hover:text-brand-700">
                                Publish a ride
                            </a>
                            <a href="{{ route('dashboards.traveler') }}" class="block rounded-2xl border border-slate-200 px-4 py-3 text-sm font-medium text-slate-700 transition hover:border-brand-200 hover:text-brand-700">
                                Traveler dashboard
                            </a>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>
@endsection
