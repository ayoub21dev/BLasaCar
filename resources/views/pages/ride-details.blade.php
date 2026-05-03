@php($title = $ride->departureCity?->name.' to '.$ride->arrivalCity?->name)

@extends('layouts.app')

@section('content')
    <section class="py-8 sm:py-12">
        <div class="shell page-enter">
            <div class="grid gap-8 xl:grid-cols-[minmax(0,1fr)_360px]">
                <div class="space-y-8">
                    <div class="bg-white rounded-[3.5rem] p-8 sm:p-12 lg:p-16 shadow-sm border border-slate-100 relative overflow-hidden">
                        <div class="flex flex-col gap-6 sm:flex-row sm:items-start sm:justify-between relative z-10">
                            <div>
                                <div class="inline-flex items-center gap-2 rounded-full bg-brand-100 px-4 py-2 text-[12px] font-black uppercase tracking-widest text-brand-600 mb-6">
                                    Ride details
                                </div>
                                <h1 class="break-words text-[3rem] sm:text-[4.5rem] font-black tracking-tight text-slate-900 leading-[0.95]">
                                    <span class="italic font-serif text-brand-500">{{ $ride->departureCity?->name }}</span> &rarr; {{ $ride->arrivalCity?->name }}
                                </h1>
                                <p class="mt-6 text-xl text-slate-500">
                                    {{ $ride->departure_time->format('l, d M Y') }} at <span class="font-bold text-slate-900">{{ $ride->departure_time->format('H:i') }}</span>
                                </p>
                            </div>
                            <div class="scale-110 origin-top-right">
                                @include('partials.status-chip', ['status' => $ride->status])
                            </div>
                        </div>

                        <div class="mt-12 grid gap-6 md:grid-cols-2 relative z-10">
                            <div class="bg-slate-50 rounded-[2rem] p-8 border border-slate-100">
                                <p class="text-[12px] font-black uppercase tracking-widest text-brand-600">Route</p>
                                <div class="mt-6 space-y-6">
                                    <div>
                                        <p class="text-sm text-slate-500 font-bold mb-1">Departure</p>
                                        <p class="text-2xl font-black text-slate-900">{{ $ride->departureCity?->name }}</p>
                                        <p class="text-sm text-slate-500 mt-1">{{ $ride->meeting_point }}</p>
                                    </div>
                                    <div class="h-px bg-slate-200"></div>
                                    <div>
                                        <p class="text-sm text-slate-500 font-bold mb-1">Arrival</p>
                                        <p class="text-2xl font-black text-slate-900">{{ $ride->arrivalCity?->name }}</p>
                                        <p class="text-sm text-slate-500 mt-1">Direct city-to-city trip</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-slate-50 rounded-[2rem] p-8 border border-slate-100">
                                <p class="text-[12px] font-black uppercase tracking-widest text-brand-600">Ride setup</p>
                                <div class="mt-6 grid gap-6 sm:grid-cols-2">
                                    <div>
                                        <p class="text-sm text-slate-500 font-bold mb-1">Vehicle</p>
                                        <p class="font-black text-slate-900 text-lg">{{ $ride->vehicle?->brand }} {{ $ride->vehicle?->model }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-slate-500 font-bold mb-1">Seats left</p>
                                        <p class="font-black text-slate-900 text-lg">{{ $ride->available_seats }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-slate-500 font-bold mb-1">Price per seat</p>
                                        <p class="font-black text-brand-600 text-lg">{{ number_format((float) $ride->price_per_seat, 0) }} DH</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-slate-500 font-bold mb-1">Notes</p>
                                        <p class="font-bold text-slate-700">{{ $ride->notes ?: 'No additional notes' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-[3.5rem] p-8 sm:p-12 shadow-sm border border-slate-100">
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

                <aside class="space-y-8">
                    <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-slate-100">
                        <p class="text-[12px] font-black uppercase tracking-widest text-brand-600">Booking card</p>
                        <h2 class="mt-4 text-3xl font-black text-slate-900 tracking-tight">Request a seat</h2>
                        <p class="mt-4 text-[15px] leading-relaxed text-slate-500">
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

                    <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-slate-100">
                        <p class="text-[12px] font-black uppercase tracking-widest text-brand-600">Useful links</p>
                        <div class="mt-6 space-y-4">
                            <a href="{{ route('rides.search', ['departure_city_id' => $ride->departure_city_id, 'arrival_city_id' => $ride->arrival_city_id]) }}"
                               class="flex items-center justify-between rounded-2xl bg-slate-50 px-6 py-4 text-[15px] font-bold text-slate-700 transition hover:bg-slate-100 group">
                                More rides on this route
                                <svg class="h-5 w-5 text-slate-400 group-hover:text-brand-500 transition-colors" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                            </a>
                            <a href="{{ route('dashboards.traveler') }}"
                               class="flex items-center justify-between rounded-2xl bg-slate-50 px-6 py-4 text-[15px] font-bold text-slate-700 transition hover:bg-slate-100 group">
                                Traveler dashboard
                                <svg class="h-5 w-5 text-slate-400 group-hover:text-brand-500 transition-colors" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                            </a>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>
@endsection
