@php($title = $ride->departureCity?->name.' to '.$ride->arrivalCity?->name)

@extends('layouts.app')

@section('content')
    <section class="py-8 sm:py-12">
        <div class="shell page-enter">
            <div class="grid gap-8 xl:grid-cols-[minmax(0,1fr)_360px]">
                <div class="space-y-8">
                    <div class="surface p-5 sm:p-8">
                        <div class="flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <p class="text-sm font-semibold uppercase tracking-[0.18em] text-brand-600">Ride details</p>
                                <h1 class="mt-3 break-words text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">
                                    {{ $ride->departureCity?->name }} &rarr; {{ $ride->arrivalCity?->name }}
                                </h1>
                                <p class="mt-3 text-lg text-slate-600">
                                    {{ $ride->departure_time->format('l, d M Y') }} at {{ $ride->departure_time->format('H:i') }}
                                </p>
                            </div>
                            @include('partials.status-chip', ['status' => $ride->status])
                        </div>

                        <div class="mt-8 grid gap-6 md:grid-cols-2">
                            <div class="surface-soft p-5 sm:p-6">
                                <p class="text-sm font-semibold uppercase tracking-[0.18em] text-slate-400">Route</p>
                                <div class="mt-5 space-y-5">
                                    <div>
                                        <p class="text-sm text-slate-500">Departure</p>
                                        <p class="text-2xl font-bold text-slate-900">{{ $ride->departureCity?->name }}</p>
                                        <p class="text-sm text-slate-500">{{ $ride->meeting_point }}</p>
                                    </div>
                                    <div class="h-px bg-slate-200"></div>
                                    <div>
                                        <p class="text-sm text-slate-500">Arrival</p>
                                        <p class="text-2xl font-bold text-slate-900">{{ $ride->arrivalCity?->name }}</p>
                                        <p class="text-sm text-slate-500">Direct city-to-city trip</p>
                                    </div>
                                </div>
                            </div>

                            <div class="surface-soft p-5 sm:p-6">
                                <p class="text-sm font-semibold uppercase tracking-[0.18em] text-slate-400">Ride setup</p>
                                <div class="mt-5 grid gap-4 sm:grid-cols-2">
                                    <div>
                                        <p class="text-sm text-slate-500">Vehicle</p>
                                        <p class="font-semibold text-slate-900">{{ $ride->vehicle?->brand }} {{ $ride->vehicle?->model }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-slate-500">Seats left</p>
                                        <p class="font-semibold text-slate-900">{{ $ride->available_seats }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-slate-500">Price per seat</p>
                                        <p class="font-semibold text-slate-900">{{ number_format((float) $ride->price_per_seat, 0) }} DH</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-slate-500">Notes</p>
                                        <p class="font-semibold text-slate-900">{{ $ride->notes ?: 'No additional notes' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="surface-soft p-5 sm:p-8">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-br from-brand-500 to-brand-700 text-lg font-bold text-white">
                                {{ strtoupper(substr($ride->user?->first_name ?? 'B', 0, 1).substr($ride->user?->last_name ?? 'C', 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold uppercase tracking-[0.18em] text-slate-400">Driver</p>
                                <h2 class="mt-1 text-2xl font-bold text-slate-950">{{ $ride->user?->first_name }} {{ $ride->user?->last_name }}</h2>
                                <p class="mt-1 text-sm text-slate-500">
                                    Rating {{ number_format((float) ($ride->user?->driverProfile?->avg_rating ?? 0), 1) }} &middot; {{ $ride->user?->driverProfile?->total_trips ?? 0 }} trips &middot;
                                    {{ $ride->user?->driverProfile?->cin_verified ? 'ID verified' : 'Verification pending' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <aside class="space-y-6">
                    <div class="surface-soft p-6">
                        <p class="text-sm font-semibold uppercase tracking-[0.18em] text-brand-600">Booking card</p>
                        <h2 class="mt-3 text-2xl font-bold text-slate-950">Request a seat</h2>
                        <p class="mt-3 text-sm leading-6 text-slate-500">
                            The public service layer already supports seat-request logic. This page is the frontend surface for that next step.
                        </p>

                        @php($canRequestRide = $ride->status === 'scheduled' && $ride->available_seats > 0 && $ride->departure_time->isFuture())

                        <form method="POST" action="{{ route('rides.book', $ride) }}" class="mt-6 space-y-4">
                            @csrf

                            <label class="block">
                                <span class="text-sm font-medium text-slate-600">Passengers</span>
                                <select name="seats" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-700 outline-none" @disabled(! $canRequestRide)>
                                    @for ($i = 1; $i <= min(4, $ride->available_seats); $i++)
                                        <option value="{{ $i }}" @selected(old('seats', 1) == $i)>{{ $i }} {{ \Illuminate\Support\Str::plural('seat', $i) }}</option>
                                    @endfor
                                </select>
                                @error('seats')
                                    <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
                                @enderror
                            </label>
                            @if ($canRequestRide)
                                <button type="submit" class="brand-button w-full justify-center rounded-[1.25rem]">
                                    Request this ride
                                </button>
                            @else
                                <button type="button" disabled class="inline-flex w-full items-center justify-center rounded-[1.25rem] bg-slate-200 px-5 py-3 font-semibold text-slate-500">
                                    Ride is not available
                                </button>
                            @endif
                        </form>
                    </div>

                    <div class="surface-soft p-6">
                        <p class="text-sm font-semibold uppercase tracking-[0.18em] text-brand-600">Useful links</p>
                        <div class="mt-4 space-y-3">
                            <a href="{{ route('rides.search', ['departure_city_id' => $ride->departure_city_id, 'arrival_city_id' => $ride->arrival_city_id]) }}"
                               class="block rounded-2xl border border-slate-200 px-4 py-3 text-sm font-medium text-slate-700 transition hover:border-brand-200 hover:text-brand-700">
                                More rides on this route
                            </a>
                            <a href="{{ route('dashboards.traveler') }}"
                               class="block rounded-2xl border border-slate-200 px-4 py-3 text-sm font-medium text-slate-700 transition hover:border-brand-200 hover:text-brand-700">
                                Traveler dashboard
                            </a>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>
@endsection
