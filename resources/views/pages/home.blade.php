@php($title = 'Travel between cities')

@extends('layouts.app')

@section('content')
    <main class="space-y-8 pb-32 bg-slate-50/50">
        <!-- HERO SECTION - ROUNDED STICKER STYLE -->
        <section class="shell">
            <div class="relative min-h-[700px] lg:min-h-[85vh] flex items-center overflow-hidden rounded-[5rem] bg-slate-950 shadow-2xl">
                <!-- Background Layer -->
                <div class="absolute inset-0 z-0">
                    <img src="{{ asset('images/Heropage.png') }}" 
                         class="h-full w-full object-cover object-bottom opacity-100 scale-105 hero-bg-img" 
                         alt="Moroccan cityscape">
                    <div class="absolute inset-0 bg-gradient-to-r from-slate-950/90 via-slate-950/40 to-transparent"></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/20 via-transparent to-transparent"></div>
                </div>

                <!-- Hero Content -->
                <div class="relative z-10 w-full px-12 lg:px-24 py-24 lg:py-12">
                    <div class="grid lg:grid-cols-[1.1fr_0.9fr] gap-20 items-center">
                        <div class="hero-text-content">
                            <h1 class="text-white text-[4rem] sm:text-[6rem] lg:text-[7.5rem] font-serif leading-[0.9] tracking-tighter italic font-black">
                                Reliable Travel <br>
                                <span class="not-italic">Solutions</span>
                            </h1>
                            
                            <p class="mt-10 max-w-xl text-[20px] sm:text-2xl leading-relaxed text-slate-300 font-medium opacity-90">
                                Join thousands of Moroccans traveling together. Safe, reliable, and cost-effective carpooling for all your intercity trips.
                            </p>

                            <div class="mt-14 flex flex-wrap gap-8 items-center">
                                <a href="#search" class="inline-flex h-16 items-center gap-4 rounded-full bg-white px-10 text-lg font-black text-slate-950 transition hover:bg-brand-500 hover:text-white hover:scale-105 active:scale-95 shadow-2xl group">
                                    <span>Contact Us</span>
                                    <div class="h-10 w-10 rounded-full bg-brand-500 flex items-center justify-center text-white group-hover:bg-white group-hover:text-brand-500 group-hover:rotate-45 transition-all">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                            <path d="M7 17L17 7M17 7H7M17 7V17" />
                                        </svg>
                                    </div>
                                </a>
                                <div class="flex items-center gap-4 bg-white/5 backdrop-blur-md rounded-full px-6 py-3 border border-white/10">
                                    <div class="flex -space-x-3">
                                        @foreach([1, 2, 3] as $i)
                                            <div class="h-12 w-12 rounded-full border-2 border-slate-900 bg-slate-800 flex items-center justify-center overflow-hidden">
                                                <img src="https://i.pravatar.cc/100?u={{ $i }}" alt="User">
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="text-white">
                                        <div class="flex items-center gap-1 text-brand-400">
                                            @foreach(range(1,5) as $i)
                                                <svg class="h-4 w-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                            @endforeach
                                            <span class="ml-2 font-black text-white text-lg">5.0</span>
                                        </div>
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mt-0.5">10k+ Reviews</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SEARCH CARD ON THE RIGHT -->
                        <div id="search" class="hidden lg:block">
                            <div class="bg-white rounded-[3rem] p-12 shadow-[0_50px_100px_-20px_rgba(0,0,0,0.3)] border border-slate-100">
                                <h2 class="text-3xl font-black text-slate-900 text-center mb-10 leading-tight">Get Your Free <br> Ride Quote Today!</h2>
                                <form method="GET" action="{{ route('rides.search') }}" class="space-y-6">
                                    <div class="space-y-2">
                                        <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-3">Leaving from</label>
                                        <select name="departure_city_id" class="w-full h-16 bg-slate-50 border border-slate-200 rounded-2xl px-6 font-bold text-slate-700 outline-none focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-50 transition">
                                            <option value="">Select city</option>
                                            @foreach ($cities as $city)
                                                <option value="{{ $city->id }}">{{ $city->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-3">Going to</label>
                                        <select name="arrival_city_id" class="w-full h-16 bg-slate-50 border border-slate-200 rounded-2xl px-6 font-bold text-slate-700 outline-none focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-50 transition">
                                            <option value="">Select city</option>
                                            @foreach ($cities as $city)
                                                <option value="{{ $city->id }}">{{ $city->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="grid grid-cols-2 gap-6">
                                        <div class="space-y-2">
                                            <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-3">Date</label>
                                            <input type="date" name="departure_date" class="w-full h-16 bg-slate-50 border border-slate-200 rounded-2xl px-6 font-bold text-slate-700 outline-none focus:border-brand-500 focus:bg-white transition" value="{{ date('Y-m-d') }}">
                                        </div>
                                        <div class="space-y-2">
                                            <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-3">Seats</label>
                                            <select name="seats" class="w-full h-16 bg-slate-50 border border-slate-200 rounded-2xl px-6 font-bold text-slate-700 outline-none focus:border-brand-500 focus:bg-white transition">
                                                @foreach ([1, 2, 3, 4] as $seatCount)
                                                    <option value="{{ $seatCount }}">{{ $seatCount }} seat{{ $seatCount > 1 ? 's' : '' }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <button type="submit" class="w-full h-20 bg-[#f97316] rounded-2xl text-white font-black text-xl shadow-[0_20px_40px_-10px_rgba(249,115,22,0.4)] hover:bg-[#ea580c] hover:scale-[1.02] active:scale-[0.98] transition-all duration-300 mt-6">
                                        Get Our Free Quote
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- STATISTICS SECTION -->
        <section class="shell">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8">
                @foreach ([
                    ['value' => '50K+', 'label' => 'Registered Members'],
                    ['value' => '100K', 'label' => 'Successful Rides'],
                    ['value' => '30+', 'label' => 'Cities Connected'],
                    ['value' => '4.8', 'label' => 'Average Rating']
                ] as $stat)
                    <div class="bg-white rounded-[3rem] p-10 lg:p-14 text-center transition shadow-sm hover:shadow-2xl hover:-translate-y-2 group border border-slate-100">
                        <p class="text-5xl lg:text-7xl font-black text-slate-900 group-hover:text-brand-500 transition tracking-tighter">{{ $stat['value'] }}</p>
                        <p class="mt-6 text-xs lg:text-sm font-black text-slate-400 uppercase tracking-[0.2em]">{{ $stat['label'] }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        <!-- TRUST SECTION -->
        <section class="shell py-12">
            <div class="grid lg:grid-cols-2 gap-20 items-center">
                <div>
                    <div class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-4 py-2 text-[12px] font-black uppercase tracking-widest text-slate-500 mb-6">
                        About Us
                    </div>
                    <h2 class="text-[3rem] sm:text-[4.5rem] font-black text-slate-900 leading-[0.95] tracking-tight">
                        Your <span class="text-brand-500 italic font-serif">Trusted</span> Carpooling <br>
                        Platform in Morocco
                    </h2>
                    <div class="mt-10 flex flex-wrap gap-8 items-center">
                        <div class="relative h-20 w-32 rounded-full overflow-hidden shadow-lg group">
                            <img src="{{ asset('images/Heropage.png') }}" class="h-full w-full object-cover scale-150 transition group-hover:scale-125">
                            <div class="absolute inset-0 bg-slate-900/40 flex items-center justify-center">
                                <div class="h-8 w-8 rounded-full bg-brand-500 flex items-center justify-center text-white">
                                    <svg class="h-4 w-4 fill-current ml-0.5" viewBox="0 0 20 20"><path d="M6 4l10 6-10 6V4z"/></svg>
                                </div>
                            </div>
                        </div>
                        <p class="flex-1 min-w-[300px] text-lg text-slate-500 leading-relaxed">
                            BlasaCar has built a strong reputation in Morocco for providing safe carpooling services, handling everything from intercity commutes to weekend trips with care and expertise.
                        </p>
                    </div>
                    <div class="mt-12 flex flex-wrap gap-6 items-center">
                        <a href="#" class="inline-flex h-16 items-center gap-4 rounded-full bg-slate-900 px-8 text-lg font-bold text-white transition hover:scale-105 active:scale-95 group">
                            <span>More About Us</span>
                            <div class="h-10 w-10 rounded-full bg-brand-500 flex items-center justify-center text-white group-hover:rotate-45 transition-transform">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                    <path d="M7 17L17 7M17 7H7M17 7V17" />
                                </svg>
                            </div>
                        </a>
                        <button class="flex items-center gap-3 text-lg font-bold text-slate-900 group">
                            Request A Callback
                            <svg class="h-6 w-6 text-slate-300 group-hover:text-brand-500 transition-colors" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 12h14M12 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    @foreach([
                        ['title' => 'ID Verified', 'icon' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/>'],
                        ['title' => 'Secure Rides', 'icon' => '<rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>'],
                        ['title' => 'Fast Booking', 'icon' => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>'],
                        ['title' => '24/7 Support', 'icon' => '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>']
                    ] as $feature)
                        <div class="bg-white border border-slate-100 rounded-[2rem] p-8 flex flex-col items-center justify-center text-center shadow-sm hover:shadow-md transition group">
                            <div class="h-16 w-16 bg-brand-50 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 group-hover:bg-brand-500 transition-all duration-300">
                                <svg class="h-8 w-8 text-brand-500 group-hover:text-white transition-colors" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    {!! $feature['icon'] !!}
                                </svg>
                            </div>
                            <p class="font-black text-slate-900 text-[15px] sm:text-lg uppercase tracking-tight">{{ $feature['title'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- POPULAR RIDES - ROUNDED STICKER -->
        <section class="shell">
            <div class="bg-slate-50 rounded-[3.5rem] p-12 lg:p-20 relative overflow-hidden">
                <div class="absolute top-0 right-0 h-96 w-96 bg-brand-500/10 blur-[100px] rounded-full -mr-48 -mt-48"></div>
                
                <div class="relative z-10">
                    <div class="flex flex-wrap items-end justify-between gap-10 mb-16">
                        <div class="max-w-xl">
                            <div class="inline-flex items-center gap-2 rounded-full bg-brand-100 px-4 py-2 text-[12px] font-black uppercase tracking-widest text-brand-600 mb-6">
                                Popular Rides
                            </div>
                            <h2 class="text-[3rem] sm:text-[4rem] font-black text-slate-900 leading-[0.95] tracking-tight">
                                Join these <span class="italic font-serif text-brand-500">trips</span> <br>
                                leaving soon.
                            </h2>
                        </div>
                        <a href="{{ route('rides.search') }}" class="text-lg font-bold text-slate-900 border-b-2 border-brand-500 pb-1 hover:text-brand-500 transition">View all rides</a>
                    </div>

                    <div class="grid gap-8 sm:grid-cols-2 xl:grid-cols-2">
                        @forelse ($featuredRides as $ride)
                            @include('partials.ride-card', ['ride' => $ride])
                        @empty
                            <div class="bg-white rounded-[2rem] p-12 text-center text-slate-500 lg:col-span-2 shadow-sm">
                                Seed rides are not loaded yet.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA SECTION - WHITE STICKER -->
        <section class="shell">
            <div class="bg-white rounded-[3.5rem] p-12 lg:p-24 relative overflow-hidden border border-slate-100 shadow-sm">
                <!-- Background decoration -->
                <div class="absolute top-0 right-0 h-[500px] w-[500px] bg-brand-500/5 blur-[120px] rounded-full -mr-64 -mt-64"></div>
                
                <div class="relative z-10 grid lg:grid-cols-2 gap-16 items-center">
                    <div>
                        <h2 class="text-[3.5rem] sm:text-[5rem] lg:text-[6rem] font-serif italic text-slate-900 leading-[0.95] tracking-tight">
                            Driving <span class="not-italic text-brand-500">Soon?</span>
                        </h2>
                        <p class="mt-8 text-xl text-slate-500 leading-relaxed max-w-xl">
                            Turn your empty seats into travel savings. Join thousands of drivers sharing their journeys across Morocco every day.
                        </p>
                        <div class="mt-12 flex flex-wrap gap-6">
                            <a href="{{ route('rides.publish') }}" class="inline-flex h-20 items-center gap-4 rounded-full bg-slate-950 px-12 text-xl font-black text-white transition hover:bg-brand-500 hover:scale-105 active:scale-95 shadow-2xl group">
                                <span>Offer a ride now</span>
                                <div class="h-12 w-12 rounded-full bg-white/20 flex items-center justify-center text-white group-hover:rotate-45 transition-transform">
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                        <path d="M7 17L17 7M17 7H7M17 7V17" />
                                    </svg>
                                </div>
                            </a>
                        </div>
                    </div>

                    <div class="hidden lg:block relative">
                        <img src="{{ asset('images/carRod.svg') }}" 
                             alt="Driving Illustration" 
                             class="w-full h-auto drop-shadow-[0_20px_50px_rgba(0,0,0,0.1)]">
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- GSAP Entrance Animation Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const tl = gsap.timeline({ defaults: { ease: 'power4.out', duration: 1.2 } });

            tl.from('.hero-bg-img', { scale: 1.2, duration: 2.5, opacity: 0 })
              .from('.hero-text-content > *', { x: -60, opacity: 0, stagger: 0.15 }, '-=2')
              .from('#search', { x: 60, opacity: 0, duration: 1 }, '-=1.2');
        });
    </script>
@endsection
