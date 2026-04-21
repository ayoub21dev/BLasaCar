@php
    $title = 'Admin Dashboard';
    $showFooter = false;
    $sidebarItems = [
        ['label' => 'Overview', 'route' => 'dashboards.admin', 'icon' => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="9"/><rect x="14" y="3" width="7" height="5"/><rect x="14" y="12" width="7" height="9"/><rect x="3" y="16" width="7" height="5"/></svg>'],
        ['label' => 'Search rides', 'route' => 'rides.search', 'icon' => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>'],
        ['label' => 'Back to website', 'route' => 'home', 'icon' => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>'],
    ];
@endphp

@extends('layouts.app')

@section('content')
    <section class="py-10">
        <div class="shell page-enter">
            <div class="flex gap-8">
                @include('partials.dashboard-sidebar', ['label' => 'Admin dashboard', 'items' => $sidebarItems])

                <div class="min-w-0 flex-1 space-y-6">
                    <div class="surface p-6 sm:p-8">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                            <div>
                                <p class="text-sm font-semibold uppercase tracking-[0.18em] text-brand-600">Platform overview</p>
                                <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">Operational visibility for BlassaCar.</h1>
                                <p class="mt-3 max-w-2xl text-slate-500">This dashboard surfaces moderation, user, ride, and booking data for the signed-in admin account.</p>
                            </div>
                            <a href="{{ route('rides.search') }}" class="brand-button-secondary text-sm">Inspect public search</a>
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                        <div class="stat-tile">
                            <p class="text-sm text-slate-500">Total users</p>
                            <p class="mt-2 text-3xl font-bold text-slate-950">{{ $metrics['total_users'] }}</p>
                        </div>
                        <div class="stat-tile">
                            <p class="text-sm text-slate-500">Active users</p>
                            <p class="mt-2 text-3xl font-bold text-slate-950">{{ $metrics['active_users'] }}</p>
                        </div>
                        <div class="stat-tile">
                            <p class="text-sm text-slate-500">Scheduled rides</p>
                            <p class="mt-2 text-3xl font-bold text-slate-950">{{ $metrics['scheduled_rides'] }}</p>
                        </div>
                        <div class="stat-tile">
                            <p class="text-sm text-slate-500">Bookings</p>
                            <p class="mt-2 text-3xl font-bold text-slate-950">{{ $metrics['total_bookings'] }}</p>
                        </div>
                    </div>

                    <div class="grid gap-6 xl:grid-cols-[minmax(0,1.3fr)_360px]">
                        <div class="dashboard-panel">
                            <div class="flex items-center justify-between">
                                <h2 class="text-xl font-bold text-slate-950">Recent users</h2>
                                <p class="text-sm text-slate-500">Suspended: {{ $alerts['suspended_users'] }}</p>
                            </div>
                            <div class="mt-6 overflow-hidden rounded-[1.5rem] border border-slate-200">
                                <table class="min-w-full divide-y divide-slate-200 text-sm">
                                    <thead class="bg-slate-50 text-left text-slate-500">
                                        <tr>
                                            <th class="px-5 py-4 font-semibold">User</th>
                                            <th class="px-5 py-4 font-semibold">Phone</th>
                                            <th class="px-5 py-4 font-semibold">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 bg-white">
                                        @foreach ($users as $user)
                                            <tr>
                                                <td class="px-5 py-4">
                                                    <p class="font-semibold text-slate-900">{{ $user->first_name }} {{ $user->last_name }}</p>
                                                    <p class="text-xs text-slate-500">{{ $user->email }}</p>
                                                </td>
                                                <td class="px-5 py-4 text-slate-600">{{ $user->phone }}</td>
                                                <td class="px-5 py-4">@include('partials.status-chip', ['status' => $user->account_status])</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div class="dashboard-panel">
                                <h2 class="text-xl font-bold text-slate-950">Moderation alerts</h2>
                                <div class="mt-4 space-y-4 text-sm text-slate-600">
                                    <div class="rounded-[1.25rem] bg-rose-50 px-4 py-4 text-rose-700">
                                        {{ $alerts['suspended_users'] }} suspended account{{ $alerts['suspended_users'] === 1 ? '' : 's' }} need review.
                                    </div>
                                    <div class="rounded-[1.25rem] bg-amber-50 px-4 py-4 text-amber-700">
                                        {{ $alerts['cancelled_rides'] }} cancelled ride{{ $alerts['cancelled_rides'] === 1 ? '' : 's' }} surfaced from moderation data.
                                    </div>
                                </div>
                            </div>

                            <div class="dashboard-panel">
                                <h2 class="text-xl font-bold text-slate-950">Verified drivers</h2>
                                <p class="mt-2 text-4xl font-black text-brand-700">{{ $metrics['verified_drivers'] }}</p>
                                <p class="mt-3 text-sm leading-6 text-slate-500">Pulled directly from the admin service metric based on verified driver profiles.</p>
                            </div>
                        </div>
                    </div>

                    <div class="dashboard-panel">
                        <div class="flex items-center justify-between">
                            <h2 class="text-xl font-bold text-slate-950">Recent rides</h2>
                            <p class="text-sm text-slate-500">{{ $metrics['completed_rides'] }} completed &middot; {{ $metrics['cancelled_rides'] }} cancelled</p>
                        </div>
                        <div class="mt-6 grid gap-4 lg:grid-cols-2">
                            @foreach ($rides as $ride)
                                <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-5">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="font-semibold text-slate-900">{{ $ride->departureCity?->name }} &rarr; {{ $ride->arrivalCity?->name }}</p>
                                            <p class="mt-1 text-sm text-slate-500">{{ $ride->departure_time->format('d M Y \a\t H:i') }}</p>
                                        </div>
                                        @include('partials.status-chip', ['status' => $ride->status])
                                    </div>
                                    <div class="mt-4 text-sm text-slate-600">
                                        Driver: {{ $ride->user?->first_name }} {{ $ride->user?->last_name }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
