@php
    $primaryLinks = [
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'Search rides', 'url' => route('rides.search')],
        ['label' => 'Publish a ride', 'url' => route('rides.publish')],
        ['label' => 'How it works', 'url' => route('home') . '#how-it-works'],
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
                    @php($active = request()->url() === $link['url'])
                    <a href="{{ $link['url'] }}"
                       class="px-6 py-2.5 text-[14px] font-bold tracking-tight {{ $active ? 'bg-white text-slate-950 shadow-md' : 'text-slate-500 hover:text-slate-900' }} rounded-full transition-all duration-300">
                        {{ $link['label'] }}
                    </a>
                @endforeach
            </div>

            <!-- Right CTA Section -->
            <div class="hidden lg:flex items-center gap-6">
                @guest
                    <a href="{{ route('login') }}" class="text-[14px] font-bold text-slate-500 hover:text-slate-950 transition">Log in</a>
                    <a href="{{ route('signup') }}" class="inline-flex h-12 items-center justify-center rounded-full bg-slate-950 px-8 text-[14px] font-black text-white transition hover:text-brand-500 shadow-xl">
                        Sign up
                    </a>
                @endguest

                @auth
                    <details class="group relative">
                        <summary aria-label="Open account menu" class="inline-flex h-12 w-12 cursor-pointer list-none items-center justify-center rounded-full bg-slate-950 text-white shadow-xl transition [&::-webkit-details-marker]:hidden">
                            <svg class="h-5 w-5 transition-colors group-hover:text-brand-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M20 21a8 8 0 0 0-16 0" />
                                <circle cx="12" cy="7" r="4" />
                            </svg>
                        </summary>

                        <div class="absolute right-0 top-14 z-[120] w-64 overflow-hidden rounded-[1.5rem] border border-slate-200 bg-white p-2 shadow-2xl">
                            <div class="px-4 py-3">
                                <p class="text-sm font-black text-slate-950">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</p>
                                <p class="mt-1 truncate text-xs font-medium text-slate-500">{{ auth()->user()->email }}</p>
                            </div>
                            <div class="border-t border-slate-100 py-2">
                                <a href="{{ route($dashboardRoute) }}" class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50 hover:text-brand-700">
                                    <svg class="h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="9"/><rect x="14" y="3" width="7" height="5"/><rect x="14" y="12" width="7" height="9"/><rect x="3" y="16" width="7" height="5"/></svg>
                                    My account
                                </a>
                                <a href="{{ route('account.settings.edit') }}" class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50 hover:text-brand-700">
                                    <svg class="h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.9-.3 1.7 1.7 0 0 0-1 1.6V21a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-1-1.6 1.7 1.7 0 0 0-1.9.3l-.1.1A2 2 0 1 1 4.2 17l.1-.1A1.7 1.7 0 0 0 4.6 15a1.7 1.7 0 0 0-1.6-1H3a2 2 0 1 1 0-4h.1a1.7 1.7 0 0 0 1.6-1 1.7 1.7 0 0 0-.3-1.9l-.1-.1A2 2 0 1 1 7 4.2l.1.1A1.7 1.7 0 0 0 9 4.6 1.7 1.7 0 0 0 10 3V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.6 1.7 1.7 0 0 0 1.9-.3l.1-.1A2 2 0 1 1 19.8 7l-.1.1a1.7 1.7 0 0 0-.3 1.9 1.7 1.7 0 0 0 1.6 1H21a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1Z"/></svg>
                                    Settings
                                </a>
                            </div>
                            <form method="POST" action="{{ route('logout') }}" class="border-t border-slate-100 pt-2">
                                @csrf
                                <button type="submit" class="flex w-full items-center gap-3 rounded-2xl px-4 py-3 text-left text-sm font-bold text-rose-600 transition hover:bg-rose-50">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5"/><path d="M21 12H9"/></svg>
                                    Log out
                                </button>
                            </form>
                        </div>
                    </details>
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
                            <a href="{{ $link['url'] }}"
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
                            <a href="{{ route($dashboardRoute) }}" class="flex h-14 items-center justify-center rounded-2xl bg-brand-500 text-[16px] font-bold text-white">My account</a>
                            <a href="{{ route('account.settings.edit') }}" class="flex h-14 items-center justify-center rounded-2xl border border-white/20 text-[16px] font-bold text-white">Settings</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex h-14 w-full items-center justify-center rounded-2xl border border-rose-300/30 text-[16px] font-bold text-rose-100">Log out</button>
                            </form>
                        @endauth
                    </div>
                </div>
            </details>
        </div>
    </nav>
</header>
