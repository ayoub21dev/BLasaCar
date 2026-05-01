<footer class="mt-20 border-t border-slate-200 bg-slate-100/70">
    <div class="shell py-14">
        <div class="grid gap-10 lg:grid-cols-[1.5fr_repeat(2,1fr)]">
            <div>
                <img src="{{ asset('assets/logoBlasaCar.png') }}" alt="BlassaCar logo" class="h-10 w-auto object-contain">
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
                        <a href="{{ route(auth()->user()->dashboardRoute()) }}" class="block transition hover:text-brand-700">My dashboard</a>
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
