<footer class="mt-20 border-t border-slate-200 bg-slate-100/70">
    <div class="shell py-14">
        <div class="grid gap-10 lg:grid-cols-[1.5fr_repeat(2,1fr)]">
            <div>
                <a href="{{ route('home') }}" class="flex items-center gap-2 inline-block">
                    <img src="{{ asset('assets/logoBlasaCar.png') }}" alt="BlassaCar logo" class="h-10 w-auto object-contain">
                    <span class="text-2xl font-black tracking-tight text-slate-950">Blasa<span class="text-brand-500">Car</span></span>
                </a>
                <p class="mt-6 max-w-md text-lg leading-8 text-slate-600">
                    A modern Moroccan carpooling interface focused on trust, clarity, and affordable intercity travel.
                </p>
            </div>

            <div>
                <h3 class="text-sm font-semibold uppercase tracking-[0.18em] text-slate-400">Explore</h3>
                <div class="mt-4 space-y-3 text-sm text-slate-600">
                    <a href="{{ route('home') }}" class="block transition hover:text-brand-700">Home</a>
                    <a href="{{ route('rides.search') }}" class="block transition hover:text-brand-700">Search rides</a>
                    <a href="{{ route('rides.publish') }}" class="block transition hover:text-brand-700">Publish a ride</a>
                </div>
            </div>

            <div>
                <h3 class="text-sm font-semibold uppercase tracking-[0.18em] text-slate-400">Account</h3>
                <div class="mt-4 space-y-3 text-sm text-slate-600">
                    @guest
                        <a href="{{ route('login') }}" class="block transition hover:text-brand-700">Log in</a>
                        <a href="{{ route('signup') }}" class="block transition hover:text-brand-700">Sign up</a>
                    @endguest
                    @auth
                        <a href="{{ route(auth()->user()->dashboardRoute()) }}" class="block transition hover:text-brand-700">My account</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block transition hover:text-brand-700">Log out</button>
                        </form>
                    @endauth
                    <span class="block text-slate-400">WhatsApp notifications and reviews coming next</span>
                </div>
            </div>
        </div>

        <div class="mt-12 border-t border-slate-200 pt-6 text-sm text-slate-500">
            &copy; 2026 BlassaCar. All rights reserved.
        </div>
    </div>
</footer>
