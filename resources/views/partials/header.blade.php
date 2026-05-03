@php
    $primaryLinks = [
        ['label' => 'Home', 'route' => 'home'],
        ['label' => 'Search rides', 'route' => 'rides.search'],
        ['label' => 'Publish a ride', 'route' => 'rides.publish'],
        ['label' => 'How it works', 'route' => 'home'],
    ];

    $dashboardRoute = auth()->user()?->dashboardRoute();
@endphp

<header class="w-full z-[100] px-4 sm:px-6 lg:px-8 py-6">
    <nav class="mx-auto w-full max-w-[1800px]">
        <div class="flex items-center justify-between">
            <!-- Logo Section -->
            <div class="flex-shrink-0">
                <a href="{{ route('home') }}" class="flex items-center gap-3 group" aria-label="BlasaCar home">
                    <img src="{{ asset('assets/logoBlasaCar.png') }}" alt="BlasaCar" class="h-10 w-auto">
                    <span class="text-2xl font-black tracking-tight text-slate-950">Blasa<span class="text-brand-500">Car</span></span>
                </a>
            </div>

            <!-- Centered Pill Nav -->
            <div class="hidden lg:flex items-center justify-center bg-slate-100 rounded-full p-1.5 shadow-sm">
                @foreach ($primaryLinks as $link)
                    <a href="{{ route($link['route']) }}"
                       class="px-6 py-2.5 text-[14px] font-bold tracking-tight {{ Route::is($link['route']) ? 'bg-white text-slate-950 shadow-md' : 'text-slate-500 hover:text-slate-900' }} rounded-full transition-all duration-300">
                        {{ $link['label'] }}
                    </a>
                @endforeach
            </div>

            <!-- Right CTA Section -->
            <div class="hidden lg:flex items-center gap-6">
                @guest
                    <a href="{{ route('login') }}" class="text-[14px] font-bold text-slate-500 hover:text-slate-950 transition">Log in</a>
                    <a href="{{ route('signup') }}" class="inline-flex h-12 items-center justify-center rounded-full bg-slate-950 px-8 text-[14px] font-black text-white transition hover:bg-brand-500 hover:scale-105 active:scale-95 shadow-xl">
                        Sign up
                    </a>
                @endguest

                @auth
                    <a href="{{ route($dashboardRoute) }}" aria-label="Open account area" class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-slate-950 text-white transition hover:bg-brand-500 hover:scale-105 active:scale-95 shadow-xl">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M20 21a8 8 0 0 0-16 0" />
                            <circle cx="12" cy="7" r="4" />
                        </svg>
                    </a>
                @endauth
            </div>
            
            <!-- Mobile Toggle -->
            <details class="group lg:hidden">
                <summary class="flex h-11 w-11 cursor-pointer items-center justify-center rounded-full bg-slate-100 text-slate-900 list-none [&::-webkit-details-marker]:hidden border border-slate-200">
                    <svg class="h-6 w-6 group-open:hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <line x1="3" x2="21" y1="6" y2="6" />
                        <line x1="3" x2="21" y1="12" y2="12" />
                        <line x1="3" x2="21" y1="18" y2="18" />
                    </svg>
                    <svg class="hidden h-6 w-6 group-open:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M18 6 6 18" />
                        <path d="m6 6 12 12" />
                    </svg>
                </summary>
                <div class="fixed inset-x-4 top-24 rounded-[2.5rem] border border-white/10 bg-slate-950 p-8 shadow-2xl backdrop-blur-3xl z-[101]">
                    <div class="space-y-2">
                        @foreach ($primaryLinks as $link)
                            <a href="{{ route($link['route']) }}"
                               class="block rounded-2xl px-6 py-4 text-[16px] font-bold text-white/80 transition hover:bg-white/5 hover:text-white">
                                {{ $link['label'] }}
                            </a>
                        @endforeach
                    </div>
                    <div class="mt-8 pt-8 border-t border-white/10 grid gap-4">
                        @guest
                            <a href="{{ route('login') }}" class="flex h-14 items-center justify-center rounded-2xl border border-white/20 text-[16px] font-bold text-white">Log in</a>
                            <a href="{{ route('signup') }}" class="flex h-14 items-center justify-center rounded-2xl bg-brand-500 text-[16px] font-bold text-white">Sign up</a>
                        @endguest
                        @auth
                            <a href="{{ route($dashboardRoute) }}" aria-label="Open account area" class="flex h-14 items-center justify-center rounded-2xl bg-brand-500 text-white">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M20 21a8 8 0 0 0-16 0" />
                                    <circle cx="12" cy="7" r="4" />
                                </svg>
                            </a>
                        @endauth
                    </div>
                </div>
            </details>
        </div>
    </nav>
</header>
