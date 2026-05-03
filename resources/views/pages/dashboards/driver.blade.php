@php
    $title = 'Driver Dashboard';
    $showFooter = false;
    $sidebarItems = [
        ['label' => 'Overview', 'route' => 'dashboards.driver', 'icon' => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="9"/><rect x="14" y="3" width="7" height="5"/><rect x="14" y="12" width="7" height="9"/><rect x="3" y="16" width="7" height="5"/></svg>'],
        ['label' => 'Search rides', 'route' => 'rides.search', 'icon' => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>'],
    ];
@endphp

@extends('layouts.app')

@section('content')
    <section class="py-10">
        <div class="shell page-enter">
            <div class="flex gap-8">
                @include('partials.dashboard-sidebar', ['label' => 'Driver dashboard', 'items' => $sidebarItems])

                <div class="min-w-0 flex-1 space-y-6">
                    <div class="surface p-6 sm:p-8">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                            <div>
                                <p class="text-sm font-semibold uppercase tracking-[0.18em] text-brand-600">Driver workspace</p>
                                <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">Welcome back, {{ $driver->first_name }}.</h1>
                                <p class="mt-3 max-w-2xl text-slate-500">Your driver dashboard is populated from your account, ride inventory, and booking activity.</p>
                            </div>
                            <a href="{{ route('rides.publish') }}" class="brand-button">Publish new ride</a>
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                        <div class="stat-tile">
                            <p class="text-sm text-slate-500">Published rides</p>
                            <p class="mt-2 text-3xl font-bold text-slate-950">{{ $stats['published_rides'] }}</p>
                        </div>
                        <div class="stat-tile">
                            <p class="text-sm text-slate-500">Upcoming rides</p>
                            <p class="mt-2 text-3xl font-bold text-slate-950">{{ $stats['upcoming_rides'] }}</p>
                        </div>
                        <div class="stat-tile">
                            <p class="text-sm text-slate-500">Completion rate</p>
                            <p class="mt-2 text-3xl font-bold text-slate-950">{{ $stats['completion_rate'] }}%</p>
                        </div>
                        <div class="stat-tile">
                            <p class="text-sm text-slate-500">Handled bookings</p>
                            <p class="mt-2 text-3xl font-bold text-slate-950">{{ $stats['response_rate'] }}%</p>
                        </div>
                    </div>

                    <div class="grid gap-6 xl:grid-cols-[minmax(0,1.3fr)_360px]">
                        <div class="dashboard-panel">
                            <div class="flex items-center justify-between">
                                <h2 class="text-xl font-bold text-slate-950">Upcoming rides</h2>
                                <a href="{{ route('rides.search') }}" class="text-sm font-medium text-brand-700">View public search</a>
                            </div>
                            <div class="mt-6 space-y-4">
                                @forelse ($rides->take(5) as $ride)
                                    <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-5">
                                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                            <div>
                                                <p class="font-semibold text-slate-900">{{ $ride->departureCity?->name }} &rarr; {{ $ride->arrivalCity?->name }}</p>
                                                <p class="mt-1 text-sm text-slate-500">{{ $ride->departure_time->format('d M Y \a\t H:i') }} &middot; {{ $ride->available_seats }} seats left</p>
                                            </div>
                                            @include('partials.status-chip', ['status' => $ride->status])
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-sm text-slate-500">No rides published yet.</p>
                                @endforelse
                            </div>
                        </div>

                        <div class="dashboard-panel">
                            <h2 class="text-xl font-bold text-slate-950">Weekly seats sold</h2>
                            <p class="mt-2 text-sm text-slate-500">Aggregated from your recent booking activity.</p>
                            <div class="mt-6 flex h-52 items-end gap-3">
                                @foreach ($weeklySeatSales as $day)
                                    <div class="flex flex-1 flex-col items-center gap-3">
                                        <div class="w-full rounded-t-2xl bg-gradient-to-t from-brand-600 to-brand-300" style="height: {{ $day['height'] }}%;"></div>
                                        <div class="text-center text-xs text-slate-500">
                                            <div class="font-semibold text-slate-700">{{ $day['seats'] }}</div>
                                            <div>{{ $day['label'] }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="dashboard-panel">
                        <div class="flex items-center justify-between">
                            <h2 class="text-xl font-bold text-slate-950">Recent booking requests</h2>
                            <p class="text-sm text-slate-500">{{ $bookings->count() }} total</p>
                        </div>
                        <div class="mt-6 overflow-x-auto rounded-[1.5rem] border border-slate-200">
                            <table class="min-w-full divide-y divide-slate-200 text-sm">
                                <thead class="bg-slate-50 text-left text-slate-500">
                                    <tr>
                                        <th class="px-5 py-4 font-semibold">Traveler</th>
                                        <th class="px-5 py-4 font-semibold">Route</th>
                                        <th class="px-5 py-4 font-semibold">Seats</th>
                                        <th class="px-5 py-4 font-semibold">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 bg-white">
                                    @foreach ($bookings->take(6) as $booking)
                                        <tr>
                                            <td class="px-5 py-4 font-medium text-slate-900">{{ $booking->traveler?->first_name }} {{ $booking->traveler?->last_name }}</td>
                                            <td class="px-5 py-4 text-slate-600">{{ $booking->ride?->departureCity?->name }} &rarr; {{ $booking->ride?->arrivalCity?->name }}</td>
                                            <td class="px-5 py-4 text-slate-600">{{ $booking->seats_reserved }}</td>
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
