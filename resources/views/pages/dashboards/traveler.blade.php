@php
    $title = 'Traveler Dashboard';
    $showFooter = false;
    $sidebarItems = [
        ['label' => 'Overview', 'route' => 'dashboards.traveler', 'icon' => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="9"/><rect x="14" y="3" width="7" height="5"/><rect x="14" y="12" width="7" height="9"/><rect x="3" y="16" width="7" height="5"/></svg>'],
        ['label' => 'Search rides', 'route' => 'rides.search', 'icon' => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>'],
        ['label' => 'Become a driver', 'route' => 'drivers.onboarding.create', 'icon' => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 17h2l-1.4-4.2A3 3 0 0 0 16.8 11H7.2a3 3 0 0 0-2.8 1.8L3 17h2"/><circle cx="7" cy="17" r="2"/><circle cx="17" cy="17" r="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>'],
    ];
@endphp

@extends('layouts.app')

@section('content')
    <section class="py-10">
        <div class="shell page-enter">
            <div class="flex gap-8">
                @include('partials.dashboard-sidebar', ['label' => 'Traveler dashboard', 'items' => $sidebarItems])

                <div class="min-w-0 flex-1 space-y-6">
                    <div class="surface p-6 sm:p-8">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                            <div>
                                <p class="text-sm font-semibold uppercase tracking-[0.18em] text-brand-600">Traveler workspace</p>
                                <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">Hello, {{ $traveler->first_name }}.</h1>
                                <p class="mt-3 max-w-2xl text-slate-500">Manage your trips and booking history from one place.</p>
                            </div>
                            <div class="flex flex-wrap gap-3">
                                <a href="{{ route('rides.search') }}" class="brand-button">Find a ride</a>
                                <a href="{{ route('drivers.onboarding.create') }}" class="brand-button-secondary">Become a driver</a>
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                        <div class="stat-tile">
                            <p class="text-sm text-slate-500">Upcoming trips</p>
                            <p class="mt-2 text-3xl font-bold text-slate-950">{{ $stats['upcoming_trips'] }}</p>
                        </div>
                        <div class="stat-tile">
                            <p class="text-sm text-slate-500">Completed trips</p>
                            <p class="mt-2 text-3xl font-bold text-slate-950">{{ $stats['completed_trips'] }}</p>
                        </div>
                        <div class="stat-tile">
                            <p class="text-sm text-slate-500">Cancelled trips</p>
                            <p class="mt-2 text-3xl font-bold text-slate-950">{{ $stats['cancelled_trips'] }}</p>
                        </div>
                        <div class="stat-tile">
                            <p class="text-sm text-slate-500">Avg driver rating</p>
                            <p class="mt-2 text-3xl font-bold text-slate-950">{{ $stats['avg_driver_rating'] }}</p>
                        </div>
                    </div>

                    <div class="grid gap-6 xl:grid-cols-[minmax(0,1.25fr)_360px]">
                        <div class="dashboard-panel">
                            <h2 class="text-xl font-bold text-slate-950">Next confirmed trip</h2>
                            @php($nextTrip = $upcomingBookings->first())
                            @if ($nextTrip)
                                <div class="mt-6 rounded-[1.75rem] border border-brand-200 bg-brand-50 p-6">
                                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                        <div class="min-w-0">
                                            <p class="break-words text-2xl font-bold text-slate-950">{{ $nextTrip->ride?->departureCity?->name }} &rarr; {{ $nextTrip->ride?->arrivalCity?->name }}</p>
                                            <p class="mt-2 text-sm text-slate-600">{{ $nextTrip->ride?->departure_time?->format('d M Y \a\t H:i') }} &middot; {{ $nextTrip->ride?->meeting_point }}</p>
                                        </div>
                                        @include('partials.status-chip', ['status' => $nextTrip->status])
                                    </div>
                                    <div class="mt-5 grid gap-3 sm:grid-cols-3">
                                        <div class="rounded-2xl bg-white px-4 py-4">
                                            <p class="text-xs uppercase tracking-[0.14em] text-slate-400">Driver</p>
                                            <p class="mt-2 font-semibold text-slate-900">{{ $nextTrip->ride?->user?->first_name }} {{ $nextTrip->ride?->user?->last_name }}</p>
                                        </div>
                                        <div class="rounded-2xl bg-white px-4 py-4">
                                            <p class="text-xs uppercase tracking-[0.14em] text-slate-400">Seats</p>
                                            <p class="mt-2 font-semibold text-slate-900">{{ $nextTrip->seats_reserved }}</p>
                                        </div>
                                        <div class="rounded-2xl bg-white px-4 py-4">
                                            <p class="text-xs uppercase tracking-[0.14em] text-slate-400">Price</p>
                                            <p class="mt-2 font-semibold text-slate-900">{{ number_format((float) ($nextTrip->ride?->price_per_seat ?? 0), 0) }} DH</p>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <p class="mt-4 text-sm text-slate-500">No upcoming trips are scheduled for this account yet.</p>
                            @endif
                        </div>

                        <div class="space-y-6">
                            <div class="dashboard-panel">
                                <h2 class="text-xl font-bold text-slate-950">Quick links</h2>
                                <div class="mt-4 space-y-3">
                                    <a href="{{ route('rides.search') }}" class="block rounded-2xl border border-slate-200 px-4 py-3 text-sm font-medium text-slate-700 transition hover:border-brand-200 hover:text-brand-700">
                                        Search more rides
                                    </a>
                                    <a href="{{ route('drivers.onboarding.create') }}" class="block rounded-2xl border border-slate-200 px-4 py-3 text-sm font-medium text-slate-700 transition hover:border-brand-200 hover:text-brand-700">
                                        Become a driver
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="dashboard-panel">
                        <div class="flex items-center justify-between">
                            <h2 class="text-xl font-bold text-slate-950">Booking history</h2>
                            <p class="text-sm text-slate-500">{{ $bookings->count() }} booking{{ $bookings->count() === 1 ? '' : 's' }}</p>
                        </div>
                        <div class="mt-6 overflow-x-auto rounded-[1.5rem] border border-slate-200">
                            <table class="min-w-full divide-y divide-slate-200 text-sm">
                                <thead class="bg-slate-50 text-left text-slate-500">
                                    <tr>
                                        <th class="px-5 py-4 font-semibold">Route</th>
                                        <th class="px-5 py-4 font-semibold">Date</th>
                                        <th class="px-5 py-4 font-semibold">Driver</th>
                                        <th class="px-5 py-4 font-semibold">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 bg-white">
                                    @foreach ($bookings as $booking)
                                        <tr>
                                            <td class="px-5 py-4 font-medium text-slate-900">{{ $booking->ride?->departureCity?->name }} &rarr; {{ $booking->ride?->arrivalCity?->name }}</td>
                                            <td class="px-5 py-4 text-slate-600">{{ $booking->ride?->departure_time?->format('d M Y \a\t H:i') }}</td>
                                            <td class="px-5 py-4 text-slate-600">{{ $booking->ride?->user?->first_name }} {{ $booking->ride?->user?->last_name }}</td>
                                            <td class="px-5 py-4">@include('partials.status-chip', ['status' => $booking->status])</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
