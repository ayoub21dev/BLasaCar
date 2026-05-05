<div class="surface p-6 sm:p-8">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.18em] text-brand-600">Platform overview</p>
            <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">Admin workspace</h1>
            <p class="mt-3 max-w-2xl text-slate-500">Review driver identities, inspect users, and monitor ride activity from one place.</p>
        </div>
        <a href="{{ route('dashboards.admin.driver-verification') }}" class="brand-button-secondary text-sm">Review drivers</a>
    </div>
</div>

<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <div class="stat-tile">
        <p class="text-sm text-slate-500">Total users</p>
        <p class="mt-2 text-3xl font-bold text-slate-950">{{ $metrics['total_users'] }}</p>
    </div>
    <div class="stat-tile">
        <p class="text-sm text-slate-500">Pending ID checks</p>
        <p class="mt-2 text-3xl font-bold text-slate-950">{{ $metrics['pending_driver_verifications'] }}</p>
    </div>
    <div class="stat-tile">
        <p class="text-sm text-slate-500">Verified drivers</p>
        <p class="mt-2 text-3xl font-bold text-slate-950">{{ $metrics['verified_drivers'] }}</p>
    </div>
    <div class="stat-tile">
        <p class="text-sm text-slate-500">Bookings</p>
        <p class="mt-2 text-3xl font-bold text-slate-950">{{ $metrics['total_bookings'] }}</p>
    </div>
</div>

<div class="grid gap-4 lg:grid-cols-3">
    <a href="{{ route('dashboards.admin.driver-verification') }}" class="dashboard-panel block transition hover:border-brand-200">
        <p class="text-sm font-black uppercase tracking-[0.14em] text-amber-600">Verification</p>
        <h2 class="mt-3 text-xl font-bold text-slate-950">Driver ID reviews</h2>
        <p class="mt-2 text-sm leading-6 text-slate-500">{{ $pendingDriverProfiles->count() }} driver profile{{ $pendingDriverProfiles->count() === 1 ? '' : 's' }} waiting for review.</p>
    </a>

    <a href="{{ route('dashboards.admin.users') }}" class="dashboard-panel block transition hover:border-brand-200">
        <p class="text-sm font-black uppercase tracking-[0.14em] text-brand-600">Users</p>
        <h2 class="mt-3 text-xl font-bold text-slate-950">User records</h2>
        <p class="mt-2 text-sm leading-6 text-slate-500">{{ $metrics['active_users'] }} active and {{ $alerts['suspended_users'] }} suspended accounts.</p>
    </a>

    <a href="{{ route('dashboards.admin.rides') }}" class="dashboard-panel block transition hover:border-brand-200">
        <p class="text-sm font-black uppercase tracking-[0.14em] text-slate-500">Rides</p>
        <h2 class="mt-3 text-xl font-bold text-slate-950">Ride activity</h2>
        <p class="mt-2 text-sm leading-6 text-slate-500">{{ $metrics['completed_rides'] }} completed and {{ $metrics['cancelled_rides'] }} cancelled rides.</p>
    </a>
</div>
