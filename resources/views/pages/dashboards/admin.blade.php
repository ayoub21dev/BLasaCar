@php
    $title = 'Admin Dashboard';
    $showFooter = false;
    $sidebarItems = [
        ['label' => 'Overview', 'route' => 'dashboards.admin', 'icon' => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="9"/><rect x="14" y="3" width="7" height="5"/><rect x="14" y="12" width="7" height="9"/><rect x="3" y="16" width="7" height="5"/></svg>'],
        ['label' => 'Driver verification', 'route' => 'dashboards.admin.driver-verification', 'icon' => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4"/><path d="M12 3 4 7v5c0 5 3.4 8.3 8 9 4.6-.7 8-4 8-9V7l-8-4Z"/></svg>'],
        ['label' => 'All users', 'route' => 'dashboards.admin.users', 'icon' => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>'],
        ['label' => 'Ride activity', 'route' => 'dashboards.admin.rides', 'icon' => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 17h2l-1.4-4.2A3 3 0 0 0 16.8 11H7.2a3 3 0 0 0-2.8 1.8L3 17h2"/><circle cx="7" cy="17" r="2"/><circle cx="17" cy="17" r="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>'],
        ['label' => 'Search rides', 'route' => 'rides.search', 'icon' => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>'],
    ];

    $sectionView = match ($section) {
        'driver-verification' => 'pages.dashboards.admin.driver-verification',
        'users' => 'pages.dashboards.admin.users',
        'rides' => 'pages.dashboards.admin.rides',
        default => 'pages.dashboards.admin.overview',
    };
@endphp

@extends('layouts.app')

@section('content')
    <section class="py-10">
        <div class="shell page-enter">
            <div class="flex gap-8">
                @include('partials.dashboard-sidebar', ['label' => 'Admin dashboard', 'items' => $sidebarItems])

                <div class="min-w-0 flex-1 space-y-6">
                    @if (session('status'))
                        <div class="rounded-[1.25rem] border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-700">
                            {{ session('status') }}
                        </div>
                    @endif

                    @error('driver_profile')
                        <div class="rounded-[1.25rem] border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-bold text-rose-700">
                            {{ $message }}
                        </div>
                    @enderror

                    @include($sectionView)
                </div>
            </div>
        </div>
    </section>
@endsection
