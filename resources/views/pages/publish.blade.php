@php($title = 'Publish a Ride')

@extends('layouts.app')

@section('content')
    <section class="py-8 sm:py-12">
        <div class="shell page-enter">
            <div class="mx-auto max-w-6xl">
                <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_320px]">
                    <div class="surface overflow-hidden">
                        <div class="bg-gradient-to-r from-brand-600 to-brand-700 px-5 py-7 text-white sm:px-8 sm:py-8">
                            <p class="text-sm font-semibold uppercase tracking-[0.18em] text-white/70">Driver flow</p>
                            <h1 class="mt-3 text-3xl font-black tracking-tight sm:text-4xl">Publish a ride</h1>
                            <p class="mt-3 max-w-2xl text-white/80">This page translates your mockup into a real Laravel surface so the ride-posting flow can be connected next.</p>
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
                        <div class="surface-soft p-6">
                            <p class="text-sm font-semibold uppercase tracking-[0.18em] text-brand-600">Design carry-over</p>
                            <p class="mt-4 text-sm leading-6 text-slate-600">
                                This page keeps the rounded, calm, sky-blue form treatment from your HTML maquettage while moving it into the Laravel asset pipeline.
                            </p>
                        </div>

                        <div class="surface-soft p-6">
                            <p class="text-sm font-semibold uppercase tracking-[0.18em] text-brand-600">Related routes</p>
                            <div class="mt-4 space-y-3">
                                <a href="{{ route('dashboards.driver') }}" class="block rounded-2xl border border-slate-200 px-4 py-3 text-sm font-medium text-slate-700 transition hover:border-brand-200 hover:text-brand-700">
                                    Driver dashboard
                                </a>
                                <a href="{{ route('rides.search') }}" class="block rounded-2xl border border-slate-200 px-4 py-3 text-sm font-medium text-slate-700 transition hover:border-brand-200 hover:text-brand-700">
                                    Search results
                                </a>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </section>
@endsection
