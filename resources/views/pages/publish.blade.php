@php($title = 'Publish a Ride')

@extends('layouts.app')

@section('content')
    <section class="py-8 sm:py-12">
        <div class="shell page-enter">
            <div class="mx-auto max-w-6xl">
                <div>
                    <div class="bg-white rounded-[3.5rem] border border-slate-100 shadow-sm overflow-hidden">
                        <div class="px-8 py-12 sm:px-16 sm:py-16 relative">
                            <div class="relative z-10">
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

                            <div class="space-y-2">
                                <span class="text-sm font-semibold text-slate-700">From</span>
                                @include('partials.city-combobox', [
                                    'cities' => $cities,
                                    'name' => 'departure_city_id',
                                    'id' => 'publish-departure-city',
                                    'placeholder' => 'Select city',
                                    'inputClass' => 'w-full rounded-[1.25rem] border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-700 outline-none',
                                ])
                                @error('departure_city_id')
                                    <p class="text-sm font-medium text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <span class="text-sm font-semibold text-slate-700">To</span>
                                @include('partials.city-combobox', [
                                    'cities' => $cities,
                                    'name' => 'arrival_city_id',
                                    'id' => 'publish-arrival-city',
                                    'placeholder' => 'Select city',
                                    'inputClass' => 'w-full rounded-[1.25rem] border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-700 outline-none',
                                ])
                                @error('arrival_city_id')
                                    <p class="text-sm font-medium text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

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
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
