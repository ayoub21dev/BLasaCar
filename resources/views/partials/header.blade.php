@php
    $primaryLinks = [
        ['label' => 'Search', 'route' => 'rides.search'],
        ['label' => 'Publish a ride', 'route' => 'rides.publish'],
    ];

    $previewLinks = [
        ['label' => 'Driver', 'route' => 'dashboards.driver'],
        ['label' => 'Traveler', 'route' => 'dashboards.traveler'],
        ['label' => 'Admin', 'route' => 'dashboards.admin'],
    ];
@endphp

<header class="sticky top-0 z-50 border-b border-white/70 bg-slate-50/90 backdrop-blur-xl">
    <nav class="shell">
        <div class="flex min-h-20 items-center justify-between gap-6">
            <a href="{{ route('home') }}" class="flex items-center gap-3" aria-label="BlassaCar home">
                <img src="{{ asset('assets/blasacar-logo.png') }}" alt="BlassaCar logo" class="h-12 w-auto object-contain">
            </a>

            <div class="hidden items-center gap-2 lg:flex">
                @foreach ($primaryLinks as $link)
                    <a href="{{ route($link['route']) }}"
                       class="nav-link {{ request()->routeIs($link['route']) ? 'nav-link-active' : '' }}">
                        {{ $link['label'] }}
                    </a>
                @endforeach
            </div>

            <div class="hidden items-center gap-3 lg:flex">
                @guest
                    <a href="{{ route('login') }}"
                       class="rounded-full px-4 py-2 text-sm font-medium text-slate-600 transition hover:text-brand-700">
                        Log in
                    </a>
                    <a href="{{ route('signup') }}" class="brand-button text-sm">
                        Sign up
                    </a>
                @endguest

                @auth
                    @php
                        $user = auth()->user();
                        $dashboardRoute = 'dashboards.traveler';
                        if ($user->account_status === 'admin' || current(preg_grep('/admin/i', $user->getAttributes() ?? []))) {
                            $dashboardRoute = 'dashboards.admin';
                        } elseif ($user->driverProfile) {
                            $dashboardRoute = 'dashboards.driver';
                        }
                    @endphp
                    <a href="{{ route($dashboardRoute) }}" class="nav-link font-bold text-brand-700">
                        My Dashboard
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="nav-link">
                            Log out
                        </button>
                    </form>
                @endauth
            </div>

            <details class="group lg:hidden">
                <summary class="flex h-12 w-12 cursor-pointer items-center justify-center rounded-full border border-slate-200 bg-white text-slate-600 shadow-sm list-none [&::-webkit-details-marker]:hidden">
                    <svg class="h-6 w-6 group-open:hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="3" x2="21" y1="6" y2="6" />
                        <line x1="3" x2="21" y1="12" y2="12" />
                        <line x1="3" x2="21" y1="18" y2="18" />
                    </svg>
                    <svg class="hidden h-6 w-6 group-open:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 6 6 18" />
                        <path d="m6 6 12 12" />
                    </svg>
                </summary>
                <div class="absolute inset-x-4 top-[calc(100%+0.75rem)] rounded-[1.75rem] border border-slate-200 bg-white p-4 shadow-2xl shadow-slate-900/10">
                    <div class="space-y-2">
                        @foreach ($primaryLinks as $link)
                            <a href="{{ route($link['route']) }}"
                               class="block rounded-2xl px-4 py-3 text-sm font-medium text-slate-700 transition hover:bg-brand-50 hover:text-brand-700">
                                {{ $link['label'] }}
                            </a>
                        @endforeach
                    </div>
                    
                    <div class="mt-4 grid gap-3 border-t border-slate-100 pt-4">
                        @guest
                            <div class="grid grid-cols-2 gap-3">
                                <a href="{{ route('login') }}" class="brand-button-secondary text-sm">
                                    Log in
                                </a>
                                <a href="{{ route('signup') }}" class="brand-button text-sm">
                                    Sign up
                                </a>
                            </div>
                        @endguest
                        
                        @auth
                            <a href="{{ route($dashboardRoute) }}" class="brand-button text-sm justify-center">
                                My Dashboard
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="w-full">
                                @csrf
                                <button type="submit" class="brand-button-secondary text-sm w-full justify-center">
                                    Log out
                                </button>
                            </form>
                        @endauth
                    </div>
                </div>
            </details>
        </div>
    </nav>
</header>
