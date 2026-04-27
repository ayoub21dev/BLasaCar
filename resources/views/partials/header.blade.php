@php
    $primaryLinks = [
        ['label' => 'Search rides', 'route' => 'rides.search'],
        ['label' => 'Publish a ride', 'route' => 'rides.publish'],
        ['label' => 'How it works', 'route' => 'home'], // Placeholder for now
    ];

    $dashboardRoute = auth()->user()?->dashboardRoute();
@endphp

<header class="sticky top-0 z-50 border-b border-slate-100 bg-white/90 backdrop-blur-xl">
    <nav class="shell">
        <div class="flex min-h-24 items-center justify-between">
            <!-- Logo Section -->
            <div class="flex-shrink-0">
                <a href="{{ route('home') }}" class="flex items-center gap-3" aria-label="BlasaCar home">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#0369a1] text-white shadow-lg shadow-blue-900/10">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/>
                        </svg>
                    </div>
                    <span class="text-2xl font-black tracking-tight text-[#0f172a]">BlasaCar</span>
                </a>
            </div>

            <!-- Navigation Links - Centered -->
            <div class="hidden flex-1 items-center justify-center gap-10 lg:flex">
                @foreach ($primaryLinks as $link)
                    <a href="{{ route($link['route']) }}"
                       class="text-[15px] font-bold text-slate-500 transition hover:text-[#0369a1]">
                        {{ $link['label'] }}
                    </a>
                @endforeach
            </div>

            <!-- Auth Section -->
            <div class="flex items-center gap-8 flex-shrink-0">
                @guest
                    <a href="{{ route('login') }}"
                       class="text-[15px] font-bold text-slate-500 transition hover:text-[#0369a1]">
                        Log in
                    </a>
                    <a href="{{ route('signup') }}" class="inline-flex h-12 items-center justify-center rounded-xl bg-[#0369a1] px-8 text-[15px] font-bold text-white transition hover:bg-[#0284c7] shadow-lg shadow-blue-100">
                        Sign up
                    </a>
                @endguest

                @auth
                    <a href="{{ route($dashboardRoute) }}" class="text-[15px] font-bold text-[#0369a1]">
                        My Dashboard
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-[15px] font-bold text-slate-500 transition hover:text-[#0369a1]">
                            Log out
                        </button>
                    </form>
                @endauth
            </div>
            
            <!-- Mobile Toggle -->
            <details class="group lg:hidden ml-4">
                <summary class="flex h-11 w-11 cursor-pointer items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 list-none [&::-webkit-details-marker]:hidden">
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
                <div class="absolute inset-x-4 top-[calc(100%+0.75rem)] rounded-2xl border border-slate-100 bg-white p-6 shadow-2xl">
                    <div class="space-y-2">
                        @foreach ($primaryLinks as $link)
                            <a href="{{ route($link['route']) }}"
                               class="block rounded-xl px-4 py-3 text-[15px] font-bold text-slate-700 transition hover:bg-slate-50">
                                {{ $link['label'] }}
                            </a>
                        @endforeach
                    </div>

                    <div class="mt-6 border-t border-slate-50 pt-6">
                        @guest
                            <div class="grid gap-3">
                                <a href="{{ route('login') }}" class="flex h-12 items-center justify-center rounded-xl border border-slate-200 text-[15px] font-bold text-slate-700">
                                    Log in
                                </a>
                                <a href="{{ route('signup') }}" class="flex h-12 items-center justify-center rounded-xl bg-[#0369a1] text-[15px] font-bold text-white">
                                    Sign up
                                </a>
                            </div>
                        @endguest
                    </div>
                </div>
            </details>
        </div>
    </nav>
</header>
