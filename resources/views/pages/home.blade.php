@php($title = 'Travel Between Cities')

@extends('layouts.app')

@section('content')
    <div class="relative">
        <!-- HERO SECTION -->
        <section class="relative min-h-[500px] overflow-hidden pb-20 lg:min-h-[420px] lg:pb-20" style="background: linear-gradient(135deg, #f8fbff 0%, #f3f9ff 52%, #eaf6ff 100%);">
            <!-- Hero Content -->
            <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="pt-24 pb-20 page-enter lg:max-w-[54%] lg:pt-8 lg:pb-20">
                    <span class="inline-flex items-center gap-2 rounded-full border border-blue-100 bg-white/70 px-4 py-2 text-[11px] font-black uppercase tracking-[0.16em] text-[#0b7bd3] shadow-sm">
                        <span class="flex h-4 w-5 items-center justify-center rounded-[4px] bg-red-600">
                            <span class="h-1.5 w-1.5 rounded-full bg-green-600"></span>
                        </span>
                        Moroccan carpooling, redesigned
                    </span>

                    <h1 class="mt-8 text-[2.8rem] sm:text-[3.4rem] lg:text-[3rem] font-black leading-[1.08] tracking-tight text-[#0f172a]">
                        Travel between cities
                        <span class="block text-[#0369a1]">simple and affordable.</span>
                    </h1>
                    
                    <p class="mt-7 max-w-md text-[16px] leading-[1.8] text-slate-500">
                        Your HTML mockups are now wired into Laravel with real cities, rides, and dashboards. Browse routes, publish trips, and use role-based workspaces.
                    </p>
                </div>
            </div>

            <!-- Hero Image -->
            <div class="hero-image-panel absolute inset-y-0 right-0 hidden lg:block">
                <img src="{{ asset('images/Heropage.png') }}" class="h-full w-full object-cover object-center" alt="Moroccan cityscape with mosque and mountains">
            </div>
        </section>

        <!-- Search Component - Floating outside the overflow-hidden section -->
        <div class="absolute bottom-0 left-1/2 z-20 w-full max-w-[960px] -translate-x-1/2 translate-y-[36%] px-4 sm:px-6">
            <!-- Tabs -->
            <div class="flex gap-1 pl-8">
                <button class="flex items-center gap-3 rounded-t-2xl bg-[#0369a1] px-10 py-5 text-sm font-bold text-white shadow-lg">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2" />
                        <circle cx="7" cy="17" r="2" />
                        <path d="M9 17h6" />
                        <circle cx="17" cy="17" r="2" />
                    </svg>
                    Find a ride
                </button>
            </div>
            
            <!-- Search Card -->
            <div class="bg-white rounded-[2.5rem] shadow-[0_40px_100px_-20px_rgba(0,0,0,0.15)] border border-slate-100/50 p-5 sm:p-6">
                <form method="GET" action="{{ route('rides.search') }}" class="grid gap-4 lg:grid-cols-[1fr_1fr_1fr_1fr_auto]">
                    <!-- Leaving from -->
                    <div class="flex items-center gap-3 rounded-2xl border border-slate-100 bg-slate-50/50 px-4 py-3.5 transition focus-within:border-blue-200 focus-within:bg-white focus-within:ring-4 focus-within:ring-blue-50/50">
                        <svg class="h-5 w-5 text-slate-300 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                            <circle cx="12" cy="10" r="3" />
                        </svg>
                        <div class="flex-1 min-w-0">
                            <div class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-0.5">Leaving from</div>
                            <select name="departure_city_id" class="w-full appearance-none bg-transparent text-[13px] font-bold text-slate-700 outline-none">
                                <option value="">Select city</option>
                                @foreach ($cities as $city)
                                    <option value="{{ $city->id }}">{{ $city->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <svg class="h-4 w-4 text-slate-300 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
                    </div>

                    <!-- Going to -->
                    <div class="flex items-center gap-3 rounded-2xl border border-slate-100 bg-slate-50/50 px-4 py-3.5 transition focus-within:border-blue-200 focus-within:bg-white focus-within:ring-4 focus-within:ring-blue-50/50">
                        <svg class="h-5 w-5 text-slate-300 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                            <circle cx="12" cy="10" r="3" />
                        </svg>
                        <div class="flex-1 min-w-0">
                            <div class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-0.5">Going to</div>
                            <select name="arrival_city_id" class="w-full appearance-none bg-transparent text-[13px] font-bold text-slate-700 outline-none">
                                <option value="">Select city</option>
                                @foreach ($cities as $city)
                                    <option value="{{ $city->id }}">{{ $city->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <svg class="h-4 w-4 text-slate-300 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
                    </div>

                    <!-- Date -->
                    <div class="flex items-center gap-3 rounded-2xl border border-slate-100 bg-slate-50/50 px-4 py-3.5 transition focus-within:border-blue-200 focus-within:bg-white focus-within:ring-4 focus-within:ring-blue-50/50">
                        <svg class="h-5 w-5 text-slate-300 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                            <line x1="16" x2="16" y1="2" y2="6" />
                            <line x1="8" x2="8" y1="2" y2="6" />
                        </svg>
                        <div class="flex-1 min-w-0">
                            <div class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-0.5">Date</div>
                            <input type="date" name="departure_date" class="w-full bg-transparent text-sm font-bold text-slate-700 outline-none" value="{{ date('Y-m-d') }}">
                        </div>
                    </div>

                    <!-- Passengers -->
                    <div class="flex items-center gap-3 rounded-2xl border border-slate-100 bg-slate-50/50 px-4 py-3.5 transition focus-within:border-blue-200 focus-within:bg-white focus-within:ring-4 focus-within:ring-blue-50/50">
                        <svg class="h-5 w-5 text-slate-300 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                            <circle cx="12" cy="7" r="4" />
                        </svg>
                        <div class="flex-1 min-w-0">
                            <div class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-0.5">Passengers</div>
                            <select name="seats" class="w-full appearance-none bg-transparent text-[13px] font-bold text-slate-700 outline-none">
                                <option value="1">1 seat</option>
                                @foreach ([2, 3, 4] as $seatCount)
                                    <option value="{{ $seatCount }}">{{ $seatCount }} seats</option>
                                @endforeach
                            </select>
                        </div>
                        <svg class="h-4 w-4 text-slate-300 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
                    </div>

                    <!-- Search Button -->
                    <button type="submit" class="flex items-center justify-center gap-2 rounded-2xl bg-[#0369a1] px-8 py-3.5 text-sm font-bold text-white transition hover:bg-[#0284c7] shadow-lg shadow-blue-200/40">
                        Search
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <circle cx="11" cy="11" r="8" />
                            <path d="m21 21-4.3-4.3" />
                        </svg>
                    </button>
                </form>
                
            </div>
        </div>
    </div>

    <!-- Spacer for the overlapping search component -->
    <div class="h-48 lg:h-56"></div>

    <section class="py-16">
        <div class="shell">
            <div class="text-center">
                <h2 class="text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">Popular Rides</h2>
                <p class="mt-3 text-lg text-slate-600">Join these trips leaving soon</p>
            </div>

            <div class="mt-12 grid gap-8 lg:grid-cols-2">
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
