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
                                <h2 class="text-3xl font-black text-slate-900 text-center mb-10 leading-tight">Find your <br> ride today!</h2>
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
                                        Find your ride
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- HOW IT WORKS SECTION -->
        <section class="shell py-12">
            <div class="relative min-h-[650px] overflow-hidden rounded-[3.5rem] px-7 py-12 sm:px-12 lg:min-h-[570px] lg:px-20 lg:py-16">


                <div class="relative z-20 max-w-[410px] lg:pb-10 lg:pr-8">
                    <p class="text-[11px] font-black uppercase tracking-[0.16em] text-[#fb6b55]">
                        How it works
                    </p>
                    <h2 class="mt-4 max-w-[360px] text-[2.9rem] font-black leading-[0.95] text-slate-950 sm:text-[4rem] lg:text-[4.6rem]">
                        Simple process
                    </h2>
                    <p class="mt-5 max-w-[360px] text-sm font-medium leading-7 text-slate-500">
                        Say goodbye to Facebook groups and endless negotiations. BlasaCar connects you in three simple steps.
                    </p>
                    <a href="{{ route('rides.search') }}" class="mt-7 inline-flex h-12 items-center justify-center rounded-full bg-[#fb6b55] px-7 text-sm font-black text-white shadow-[0_18px_40px_-18px_rgba(251,107,85,0.75)] transition hover:-translate-y-0.5 hover:bg-[#ef5d49]">
                        Get Started
                    </a>
                </div>

                <div class="absolute inset-y-12 left-[430px] right-0 hidden lg:block">
                    <svg class="absolute inset-0 z-10 h-full w-full" viewBox="0 0 1000 430" fill="none" preserveAspectRatio="none">
                        <!-- Background Numbers -->
                        <text x="105" y="292" text-anchor="middle" dominant-baseline="central" font-weight="900" fill="#f1f5f9" style="font-size: 200px; font-family: sans-serif; opacity: 1;">1</text>
                        <text x="445" y="198" text-anchor="middle" dominant-baseline="central" font-weight="900" fill="#f1f5f9" style="font-size: 200px; font-family: sans-serif; opacity: 1;">2</text>
                        <text x="790" y="108" text-anchor="middle" dominant-baseline="central" font-weight="900" fill="#f1f5f9" style="font-size: 200px; font-family: sans-serif; opacity: 1;">3</text>

                        <path d="M-80 285 C-4 335 58 330 105 292 C194 168 300 166 445 198 C562 230 654 193 720 128 C748 101 766 108 790 108 C872 108 922 109 1030 99" stroke="#fb6b55" stroke-width="5" stroke-linecap="round"/>
                        <g>
                            <circle cx="105" cy="292" r="28" fill="#f3f4f6" opacity="0.95"/>
                            <circle cx="105" cy="292" r="14" fill="#ffffff"/>
                            <circle cx="105" cy="292" r="6" fill="#cbd5e1"/>
                        </g>
                        <g>
                            <circle cx="445" cy="198" r="28" fill="#f3f4f6" opacity="0.95"/>
                            <circle cx="445" cy="198" r="14" fill="#ffffff"/>
                            <circle cx="445" cy="198" r="6" fill="#cbd5e1"/>
                        </g>
                        <g>
                            <circle cx="790" cy="108" r="28" fill="#f3f4f6" opacity="0.95"/>
                            <circle cx="790" cy="108" r="14" fill="#ffffff"/>
                            <circle cx="790" cy="108" r="6" fill="#cbd5e1"/>
                        </g>
                    </svg>

                    <div class="absolute left-[10.5%] top-[67.9%] z-20 w-[270px]">
                        <div class="relative pt-16">
                            <h3 class="text-sm font-black text-slate-950">Search a trip</h3>
                            <p class="mt-2 text-sm font-medium leading-6 text-slate-500">
                                Enter your departure, destination, travel date, and seats.
                            </p>
                        </div>
                    </div>

                    <div class="absolute left-[44.5%] top-[46%] z-20 w-[285px]">
                        <div class="relative pt-16">
                            <h3 class="text-sm font-black text-slate-950">Choose your ride</h3>
                            <p class="mt-2 text-sm font-medium leading-6 text-slate-500">
                                Compare verified drivers, prices, pickup points, and seats.
                            </p>
                        </div>
                    </div>

                    <div class="absolute left-[79%] top-[25.1%] z-20 w-[285px]">
                        <div class="relative pt-16">
                            <h3 class="text-sm font-black text-slate-950">Book and go</h3>
                            <p class="mt-2 text-sm font-medium leading-6 text-slate-500">
                                Confirm instantly and travel with support close when needed.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="relative z-10 mt-12 grid gap-8 lg:hidden">
                    @foreach ([
                        ['step' => '1', 'title' => 'Search a trip', 'copy' => 'Enter your departure, destination, travel date, and seats.'],
                        ['step' => '2', 'title' => 'Choose your ride', 'copy' => 'Compare verified drivers, prices, pickup points, and seats.'],
                        ['step' => '3', 'title' => 'Book and go', 'copy' => 'Confirm instantly and travel with support close when needed.'],
                    ] as $feature)
                        <div class="relative border-l-2 border-[#fb6b55] pl-8">
                            <span class="absolute -left-[11px] top-1 h-5 w-5 rounded-full bg-white shadow-[0_0_0_8px_rgba(15,23,42,0.06)]"></span>
                            <span class="absolute -left-[5px] top-[10px] h-2 w-2 rounded-full bg-slate-300"></span>
                            <span class="pointer-events-none absolute right-2 top-[-18px] text-7xl font-black leading-none text-slate-100">{{ $feature['step'] }}</span>
                            <h3 class="text-base font-black text-slate-950">{{ $feature['title'] }}</h3>
                            <p class="mt-2 max-w-[320px] text-sm font-medium leading-6 text-slate-500">{{ $feature['copy'] }}</p>
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
                            <a href="{{ route('rides.publish') }}" class="inline-flex h-16 items-center gap-3 rounded-full bg-slate-950 px-8 text-lg font-black text-white transition shadow-2xl group hover:shadow-lg">
                                <span>Offer a ride now</span>
                                <div class="h-10 w-10 rounded-full bg-white/20 flex items-center justify-center text-white group-hover:bg-brand-500 transition-all duration-300">
                                    <svg class="h-5 w-5 group-hover:rotate-45 transition-transform duration-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
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
