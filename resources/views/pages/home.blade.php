@php($title = 'Travel Between Cities')

@extends('layouts.app')

@section('content')
    <section class="relative overflow-hidden py-16 lg:py-24">
        <div class="hero-grid absolute inset-0"></div>
        <div class="shell page-enter relative">
            <div class="mx-auto max-w-4xl text-center">
                <span class="inline-flex items-center rounded-full border border-brand-200 bg-brand-50 px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-brand-700">
                    Moroccan carpooling, redesigned
                </span>
                <h1 class="mt-8 text-5xl font-black tracking-tight text-slate-950 sm:text-6xl lg:text-7xl">
                    Travel between cities
                    <span class="block text-brand-700">simple and affordable.</span>
                </h1>
                <p class="mx-auto mt-6 max-w-2xl text-lg leading-8 text-slate-600 sm:text-xl">
                    Your HTML mockups are now wired into Laravel with real cities, rides, and dashboards. Browse routes, publish trips, and use role-based workspaces.
                </p>
                <div class="mt-10 flex flex-col justify-center gap-4 sm:flex-row">
                    <a href="{{ route('rides.publish') }}" class="brand-button">
                        Publish a ride
                    </a>
                    <a href="{{ route('rides.search') }}" class="brand-button-secondary">
                        Explore rides
                    </a>
                </div>
            </div>

            <div class="surface mx-auto mt-14 max-w-6xl p-3 sm:p-4">
                <form method="GET" action="{{ route('rides.search') }}" class="grid gap-3 lg:grid-cols-[1fr_1fr_1fr_150px_160px]">
                    <label class="input-shell">
                        <svg class="h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                            <circle cx="12" cy="10" r="3" />
                        </svg>
                        <select name="departure_city_id" class="w-full bg-transparent text-sm font-medium text-slate-700 outline-none">
                            <option value="">Leaving from</option>
                            @foreach ($cities as $city)
                                <option value="{{ $city->id }}">{{ $city->name }}</option>
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
                                <option value="{{ $city->id }}">{{ $city->name }}</option>
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
                        <input type="date" name="departure_date" onclick="this.showPicker()" class="date-input-clean w-full bg-transparent text-sm font-medium text-slate-700 outline-none cursor-pointer">
                    </label>

                    <label class="input-shell">
                        <svg class="h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                            <circle cx="12" cy="7" r="4" />
                        </svg>
                        <select name="seats" class="w-full bg-transparent text-sm font-medium text-slate-700 outline-none">
                            @foreach ([1, 2, 3, 4] as $seatCount)
                                <option value="{{ $seatCount }}">{{ $seatCount }} {{ \Illuminate\Support\Str::plural('seat', $seatCount) }}</option>
                            @endforeach
                        </select>
                    </label>

                    <button type="submit" class="brand-button rounded-[1.4rem]">
                        Search
                    </button>
                </form>
            </div>
        </div>
    </section>

    <section class="py-16">
        <div class="shell">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.18em] text-brand-600">Popular rides</p>
                    <h2 class="mt-3 text-3xl font-bold text-slate-950 sm:text-4xl">Trips leaving soon</h2>
                </div>
                <a href="{{ route('rides.search') }}" class="brand-button-secondary text-sm">View all rides</a>
            </div>

            <div class="mt-10 grid gap-6 lg:grid-cols-2">
                @forelse ($featuredRides as $ride)
                    @include('partials.ride-card', ['ride' => $ride])
                @empty
                    <div class="surface-soft p-8 text-center text-slate-500 lg:col-span-2">
                        Seed rides are not loaded yet. Run <code class="rounded bg-slate-100 px-2 py-1 text-sm text-slate-700">php artisan db:seed</code> to fill the UI with ride data.
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="py-12">
        <div class="shell grid gap-6 xl:grid-cols-4">
            <article class="surface-soft p-8">
                <div class="text-brand-600">
                    <svg class="h-10 w-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M12 3 5 6v6c0 5 3.5 8.5 7 9 3.5-.5 7-4 7-9V6l-7-3z" />
                        <path d="m9.2 12.2 2.2 2.1 3.4-3.9" />
                    </svg>
                </div>
                <h3 class="mt-6 text-2xl font-bold text-slate-900">Verified profiles</h3>
                <p class="mt-3 leading-7 text-slate-600">Drivers are surfaced with ratings, trip history, and verification status right inside the ride flow.</p>
            </article>

            <article class="surface-soft p-8">
                <div class="text-brand-600">
                    <svg class="h-10 w-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="m13 2-8.5 10h6l-1 10L18 11h-6z" />
                    </svg>
                </div>
                <h3 class="mt-6 text-2xl font-bold text-slate-900">Fast booking surfaces</h3>
                <p class="mt-3 leading-7 text-slate-600">Search and detail pages are laid out for immediate scanning, clear pricing, and one-action booking flows.</p>
            </article>

            <article class="surface-soft p-8">
                <div class="text-brand-600">
                    <svg class="h-10 w-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M20 11.5c0 4.7-4 8.5-9 8.5-1.1 0-2.2-.2-3.2-.6L4 20.8l1.3-3.1A8 8 0 0 1 2 11.5C2 6.8 6 3 11 3s9 3.8 9 8.5z" />
                    </svg>
                </div>
                <h3 class="mt-6 text-2xl font-bold text-slate-900">Notification-ready</h3>
                <p class="mt-3 leading-7 text-slate-600">The frontend is already shaped around confirmation, reminders, and operational messaging for the next integration step.</p>
            </article>

            <article class="surface-soft p-8">
                <div class="text-brand-600">
                    <svg class="h-10 w-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="m12 3 2.8 5.7 6.2.9-4.5 4.4 1.1 6.2L12 17.3 6.4 20.2l1.1-6.2L3 9.6l6.2-.9z" />
                    </svg>
                </div>
                <h3 class="mt-6 text-2xl font-bold text-slate-900">Review-first trust</h3>
                <p class="mt-3 leading-7 text-slate-600">Rating cues and secure dashboards make the platform feel consistent with the trust goals in your analysis docs.</p>
            </article>
        </div>
    </section>

    <section class="pb-8 pt-12">
        <div class="shell">
            <div class="surface overflow-hidden p-8 lg:p-12">
                <div class="grid gap-10 lg:grid-cols-[1fr_1.1fr] lg:items-center">
                    <div>
                        <h2 class="text-3xl font-black tracking-tight sm:text-4xl lg:text-5xl" style="color: #082f49;">
                            Driving in your car
                            <span class="block">soon?</span>
                        </h2>
                        <p class="mt-6 max-w-xl text-lg leading-8 text-slate-600">
                            Let's make this your least expensive journey ever.
                        </p>
                        <div class="mt-10">
                            <a href="{{ route('rides.publish') }}"
                               class="inline-flex items-center gap-3 rounded-full bg-white px-8 py-5 text-lg font-bold text-brand-600 shadow-[0_18px_38px_-24px_rgba(14,165,233,0.55)] ring-1 ring-slate-100 transition hover:-translate-y-0.5 hover:text-brand-700">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4">
                                    <circle cx="12" cy="12" r="9" />
                                    <path d="M12 8v8M8 12h8" />
                                </svg>
                                <span>Offer a ride</span>
                            </a>
                        </div>
                    </div>

                    <div class="overflow-hidden">
                        <img src="{{ asset('images/carRod.svg') }}" class="w-full h-auto" alt="Car on road animation">
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
