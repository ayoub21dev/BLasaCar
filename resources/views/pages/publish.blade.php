@php($title = 'Publish a Ride')

@extends('layouts.app')

@section('content')
    <section class="py-8 sm:py-12">
        <div class="shell page-enter">
            <div class="mx-auto max-w-6xl">
                <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_320px]">
                    <div class="bg-white rounded-[3.5rem] border border-slate-100 shadow-sm overflow-hidden">
                        <div class="px-8 py-12 sm:px-16 sm:py-16 relative">
                            <div class="relative z-10">
                                <div class="inline-flex items-center gap-2 rounded-full bg-brand-100 px-4 py-2 text-[12px] font-black uppercase tracking-widest text-brand-600 mb-6">
                                    Driver Flow
                                </div>
                                <h1 class="text-[3rem] sm:text-[4.5rem] font-black text-slate-900 leading-[0.95] tracking-tight">
                                    Publish a <span class="italic font-serif text-brand-500">ride</span>.
                                </h1>
                                <p class="mt-6 max-w-xl text-lg text-slate-500">Share your journey, save on travel costs, and meet great people along the way.</p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('rides.publish.store') }}" class="grid gap-6 p-5 sm:p-8 lg:grid-cols-2">
                            @csrf

                            @auth
                                @if (auth()->user()->isDriver())
                                    <label class="space-y-2 lg:col-span-2">
                                        <span class="text-sm font-semibold text-slate-700">Vehicle</span>
                                        <select name="vehicle_id" class="w-full rounded-[1.25rem] border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-700 outline-none">
                                            <option value="">Select vehicle</option>
                                            @foreach ($vehicles as $vehicle)
                                                <option value="{{ $vehicle->id }}" @selected(old('vehicle_id') == $vehicle->id)>
                                                    {{ $vehicle->brand }} {{ $vehicle->model }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('vehicle_id')
                                            <p class="text-sm font-medium text-red-600">{{ $message }}</p>
                                        @enderror
                                    </label>
                                @endif
                            @endauth

                            <label class="space-y-2">
                                <span class="text-sm font-semibold text-slate-700">From</span>
                                <select name="departure_city_id" class="w-full rounded-[1.25rem] border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-700 outline-none">
                                    <option value="">Select city</option>
                                    @foreach ($cities as $city)
                                        <option value="{{ $city->id }}" @selected(old('departure_city_id') == $city->id)>{{ $city->name }}</option>
                                    @endforeach
                                </select>
                                @error('departure_city_id')
                                    <p class="text-sm font-medium text-red-600">{{ $message }}</p>
                                @enderror
                            </label>

                            <label class="space-y-2">
                                <span class="text-sm font-semibold text-slate-700">To</span>
                                <select name="arrival_city_id" class="w-full rounded-[1.25rem] border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-700 outline-none">
                                    <option value="">Select city</option>
                                    @foreach ($cities as $city)
                                        <option value="{{ $city->id }}" @selected(old('arrival_city_id') == $city->id)>{{ $city->name }}</option>
                                    @endforeach
                                </select>
                                @error('arrival_city_id')
                                    <p class="text-sm font-medium text-red-600">{{ $message }}</p>
                                @enderror
                            </label>

                            <label class="space-y-2">
                                <span class="text-sm font-semibold text-slate-700">Date</span>
                                <input type="date" name="departure_date" value="{{ old('departure_date') }}" class="w-full rounded-[1.25rem] border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-700 outline-none">
                                @error('departure_date')
                                    <p class="text-sm font-medium text-red-600">{{ $message }}</p>
                                @enderror
                            </label>

                            <label class="space-y-2">
                                <span class="text-sm font-semibold text-slate-700">Departure time</span>
                                <input type="time" name="departure_time" value="{{ old('departure_time') }}" class="w-full rounded-[1.25rem] border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-700 outline-none">
                                @error('departure_time')
                                    <p class="text-sm font-medium text-red-600">{{ $message }}</p>
                                @enderror
                            </label>

                            <label class="space-y-2">
                                <span class="text-sm font-semibold text-slate-700">Seats offered</span>
                                <select name="seats_offered" class="w-full rounded-[1.25rem] border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-700 outline-none">
                                    @foreach ([1, 2, 3, 4] as $seatCount)
                                        <option value="{{ $seatCount }}" @selected(old('seats_offered', 1) == $seatCount)>{{ $seatCount }} {{ \Illuminate\Support\Str::plural('seat', $seatCount) }}</option>
                                    @endforeach
                                </select>
                                @error('seats_offered')
                                    <p class="text-sm font-medium text-red-600">{{ $message }}</p>
                                @enderror
                            </label>

                            <label class="space-y-2">
                                <span class="text-sm font-semibold text-slate-700">Price per seat (DH)</span>
                                <input type="number" name="price_per_seat" value="{{ old('price_per_seat') }}" placeholder="70" class="w-full rounded-[1.25rem] border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-700 outline-none">
                                @error('price_per_seat')
                                    <p class="text-sm font-medium text-red-600">{{ $message }}</p>
                                @enderror
                            </label>

                            <label class="space-y-2 lg:col-span-2">
                                <span class="text-sm font-semibold text-slate-700">Meeting point</span>
                                <input type="text" name="meeting_point" value="{{ old('meeting_point') }}" placeholder="Casa Voyageurs taxi lane" class="w-full rounded-[1.25rem] border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-700 outline-none">
                                @error('meeting_point')
                                    <p class="text-sm font-medium text-red-600">{{ $message }}</p>
                                @enderror
                            </label>

                            <label class="space-y-2 lg:col-span-2">
                                <span class="text-sm font-semibold text-slate-700">Notes</span>
                                <textarea rows="5" name="notes" placeholder="Luggage policy, flexible pickup, or other details" class="w-full rounded-[1.25rem] border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-700 outline-none">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <p class="text-sm font-medium text-red-600">{{ $message }}</p>
                                @enderror
                            </label>

                            <div class="lg:col-span-2">
                                @guest
                                    <a href="{{ route('login') }}" class="brand-button w-full justify-center rounded-[1.4rem] py-4 text-base">
                                        Log in to publish
                                    </a>
                                @else
                                    @if (auth()->user()->isDriver() && $vehicles->isNotEmpty())
                                        <button type="submit" class="brand-button w-full justify-center rounded-[1.4rem] py-4 text-base">
                                            Publish my ride
                                        </button>
                                    @elseif (auth()->user()->isTraveler())
                                        <a href="{{ route('drivers.onboarding.create') }}" class="brand-button w-full justify-center rounded-[1.4rem] py-4 text-base">
                                            Become a driver first
                                        </a>
                                    @else
                                        <button type="button" disabled class="inline-flex w-full items-center justify-center rounded-[1.4rem] bg-slate-200 px-5 py-4 text-base font-semibold text-slate-500">
                                            Driver account with a vehicle required
                                        </button>
                                    @endif
                                @endguest
                                <p class="mt-4 text-center text-sm leading-6 text-slate-500">
                                    Real ride publishing is now connected for signed-in driver accounts.
                                </p>
                            </div>
                        </form>
                    </div>

                    <aside class="space-y-6">
                        <div class="bg-white rounded-[2rem] border border-slate-100 p-8 shadow-sm">
                            <p class="text-[12px] font-black uppercase tracking-widest text-brand-600">Design carry-over</p>
                            <p class="mt-4 text-[15px] leading-relaxed text-slate-500">
                                This page keeps the rounded, calm form treatment while adapting to the new high-end, high-contrast aesthetic.
                            </p>
                        </div>

                        <div class="bg-white rounded-[2rem] border border-slate-100 p-8 shadow-sm">
                            <p class="text-[12px] font-black uppercase tracking-widest text-brand-600">Related routes</p>
                            <div class="mt-6 space-y-4">
                                @auth
                                    @if (auth()->user()->isDriver())
                                        <a href="{{ route('dashboards.driver') }}" class="flex items-center justify-between rounded-2xl bg-slate-50 px-6 py-4 text-[15px] font-bold text-slate-700 transition hover:bg-slate-100 group">
                                            Driver dashboard
                                            <svg class="h-5 w-5 text-slate-400 group-hover:text-brand-500 transition-colors" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                                        </a>
                                    @elseif (auth()->user()->isTraveler())
                                        <a href="{{ route('drivers.onboarding.create') }}" class="flex items-center justify-between rounded-2xl bg-slate-50 px-6 py-4 text-[15px] font-bold text-slate-700 transition hover:bg-slate-100 group">
                                            Become a driver
                                            <svg class="h-5 w-5 text-slate-400 group-hover:text-brand-500 transition-colors" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                                        </a>
                                    @endif
                                @endauth
                                <a href="{{ route('rides.search') }}" class="flex items-center justify-between rounded-2xl bg-slate-50 px-6 py-4 text-[15px] font-bold text-slate-700 transition hover:bg-slate-100 group">
                                    Search results
                                    <svg class="h-5 w-5 text-slate-400 group-hover:text-brand-500 transition-colors" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                                </a>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </section>
@endsection
