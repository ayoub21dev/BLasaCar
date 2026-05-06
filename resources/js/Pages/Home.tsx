import { Link, router } from '@inertiajs/react';
import { FormEvent, useState } from 'react';
import { CityCombobox } from '../components/CityCombobox';
import { Layout } from '../components/Layout';
import { RideCard } from '../components/RideCard';
import { asset, path } from '../routes';
import { City, Ride } from '../types';

type HomeProps = {
    cities: City[];
    featuredRides: Ride[];
    today: string;
};

export default function Home({ cities, featuredRides, today }: HomeProps) {
    const [filters, setFilters] = useState({ departure_city_id: '', arrival_city_id: '', departure_date: today, seats: '1' });
    const submit = (event: FormEvent) => {
        event.preventDefault();
        router.get(path('rides.search'), filters);
    };

    return (
        <Layout title="Travel between cities">
            <main className="space-y-8 bg-slate-50/50 pb-32">
                <section className="shell">
                    <div className="relative flex min-h-[700px] items-center overflow-hidden rounded-[5rem] bg-slate-950 shadow-2xl lg:min-h-[85vh]">
                        <div className="absolute inset-0 z-0">
                            <img src={asset('images/Heropage.png')} className="hero-bg-img h-full w-full scale-105 object-cover object-bottom opacity-100" alt="Moroccan cityscape" />
                            <div className="absolute inset-0 bg-gradient-to-r from-slate-950/90 via-slate-950/40 to-transparent" />
                            <div className="absolute inset-0 bg-gradient-to-t from-slate-950/20 via-transparent to-transparent" />
                        </div>
                        <div className="relative z-10 w-full px-12 py-24 lg:px-24 lg:py-12">
                            <div className="grid items-center gap-20 lg:grid-cols-[1.1fr_0.9fr]">
                                <div className="hero-text-content">
                                    <h1 className="font-serif text-[4rem] font-black italic leading-[0.9] tracking-tighter text-white sm:text-[6rem] lg:text-[7.5rem]">
                                        Reliable Travel <br /><span className="not-italic">Solutions</span>
                                    </h1>
                                    <p className="mt-10 max-w-xl text-[20px] font-medium leading-relaxed text-slate-300 opacity-90 sm:text-2xl">
                                        Join thousands of Moroccans traveling together. Safe, reliable, and cost-effective carpooling for all your intercity trips.
                                    </p>
                                    <div className="mt-14 flex flex-wrap items-center gap-8">
                                        <div className="flex items-center gap-4 rounded-full border border-white/10 bg-white/5 px-6 py-3 backdrop-blur-md">
                                            <div className="flex -space-x-3">
                                                {[1, 2, 3].map((i) => (
                                                    <div key={i} className="flex h-12 w-12 items-center justify-center overflow-hidden rounded-full border-2 border-slate-900 bg-slate-800">
                                                        <img src={`https://i.pravatar.cc/100?u=${i}`} alt="User" />
                                                    </div>
                                                ))}
                                            </div>
                                            <div className="text-white">
                                                <div className="flex items-center gap-1 text-brand-400">
                                                    {Array.from({ length: 5 }).map((_, i) => <Star key={i} />)}
                                                    <span className="ml-2 text-lg font-black text-white">5.0</span>
                                                </div>
                                                <p className="mt-0.5 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">10k+ Reviews</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="search" className="hidden lg:block">
                                    <div className="rounded-[3rem] border border-slate-100 bg-white p-12 shadow-[0_50px_100px_-20px_rgba(0,0,0,0.3)]">
                                        <h2 className="mb-10 text-center text-3xl font-black leading-tight text-slate-900">Find your <br /> ride today!</h2>
                                        <form onSubmit={submit} className="space-y-6">
                                            <SearchCity label="Leaving from" cities={cities} value={filters.departure_city_id} onChange={(value) => setFilters({ ...filters, departure_city_id: value })} />
                                            <SearchCity label="Going to" cities={cities} value={filters.arrival_city_id} onChange={(value) => setFilters({ ...filters, arrival_city_id: value })} />
                                            <div className="grid grid-cols-2 gap-6">
                                                <label className="space-y-2">
                                                    <span className="ml-3 text-[11px] font-black uppercase tracking-widest text-slate-400">Date</span>
                                                    <input type="date" value={filters.departure_date} onChange={(event) => setFilters({ ...filters, departure_date: event.target.value })} className="h-16 w-full rounded-2xl border border-slate-200 bg-slate-50 px-6 font-bold text-slate-700 outline-none transition focus:border-brand-500 focus:bg-white" />
                                                </label>
                                                <label className="space-y-2">
                                                    <span className="ml-3 text-[11px] font-black uppercase tracking-widest text-slate-400">Seats</span>
                                                    <select value={filters.seats} onChange={(event) => setFilters({ ...filters, seats: event.target.value })} className="h-16 w-full rounded-2xl border border-slate-200 bg-slate-50 px-6 font-bold text-slate-700 outline-none transition focus:border-brand-500 focus:bg-white">
                                                        {[1, 2, 3, 4].map((seat) => <option key={seat} value={seat}>{seat} seat{seat > 1 ? 's' : ''}</option>)}
                                                    </select>
                                                </label>
                                            </div>
                                            <button type="submit" className="mt-6 h-20 w-full rounded-2xl bg-[#f97316] text-xl font-black text-white shadow-[0_20px_40px_-10px_rgba(249,115,22,0.4)] transition-all duration-300 hover:scale-[1.02] hover:bg-[#ea580c] active:scale-[0.98]">
                                                Find your ride
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <HowItWorks />
                <section className="shell">
                    <div className="relative overflow-hidden rounded-[3.5rem] bg-slate-50 p-12 lg:p-20">
                        <div className="absolute right-0 top-0 -mr-48 -mt-48 h-96 w-96 rounded-full bg-brand-500/10 blur-[100px]" />
                        <div className="relative z-10">
                            <div className="mb-16 flex flex-wrap items-end justify-between gap-10">
                                <div className="max-w-xl">
                                    <div className="mb-6 inline-flex items-center gap-2 rounded-full bg-brand-100 px-4 py-2 text-[12px] font-black uppercase tracking-widest text-brand-600">Popular Rides</div>
                                    <h2 className="text-[3rem] font-black leading-[0.95] tracking-tight text-slate-900 sm:text-[4rem]">
                                        Join these <span className="font-serif italic text-brand-500">trips</span> <br /> leaving soon.
                                    </h2>
                                </div>
                                <Link href={path('rides.search')} className="border-b-2 border-brand-500 pb-1 text-lg font-bold text-slate-900 transition hover:text-brand-500">View all rides</Link>
                            </div>
                            <div className="grid gap-8 sm:grid-cols-2 xl:grid-cols-2">
                                {featuredRides.length > 0 ? featuredRides.map((ride) => <RideCard key={ride.id} ride={ride} />) : <div className="rounded-[2rem] bg-white p-12 text-center text-slate-500 shadow-sm lg:col-span-2">Seed rides are not loaded yet.</div>}
                            </div>
                        </div>
                    </div>
                </section>

                <section className="shell">
                    <div className="relative overflow-hidden rounded-[3.5rem] border border-slate-100 bg-white p-12 shadow-sm lg:p-24">
                        <div className="absolute right-0 top-0 -mr-64 -mt-64 h-[500px] w-[500px] rounded-full bg-brand-500/5 blur-[120px]" />
                        <div className="relative z-10 grid items-center gap-16 lg:grid-cols-2">
                            <div>
                                <h2 className="font-serif text-[3.5rem] italic leading-[0.95] tracking-tight text-slate-900 sm:text-[5rem] lg:text-[6rem]">
                                    Driving <span className="not-italic text-brand-500">Soon?</span>
                                </h2>
                                <p className="mt-8 max-w-xl text-xl leading-relaxed text-slate-500">Turn your empty seats into travel savings. Join thousands of drivers sharing their journeys across Morocco every day.</p>
                                <div className="mt-12 flex flex-wrap gap-6">
                                    <Link href={path('rides.publish')} className="group inline-flex h-16 items-center gap-3 rounded-full bg-slate-950 px-8 text-lg font-black text-white shadow-2xl transition hover:shadow-lg">
                                        <span>Offer a ride now</span>
                                        <div className="flex h-10 w-10 items-center justify-center rounded-full bg-white/20 text-white transition-all duration-300 group-hover:bg-brand-500">
                                            <svg className="h-5 w-5 transition-transform duration-300 group-hover:rotate-45" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="3"><path d="M7 17L17 7M17 7H7M17 7V17" /></svg>
                                        </div>
                                    </Link>
                                </div>
                            </div>
                            <div className="relative hidden lg:block">
                                <img src={asset('images/carRod.svg')} alt="Driving Illustration" className="h-auto w-full drop-shadow-[0_20px_50px_rgba(0,0,0,0.1)]" />
                            </div>
                        </div>
                    </div>
                </section>
            </main>
        </Layout>
    );
}

function SearchCity({ label, cities, value, onChange }: { label: string; cities: City[]; value: string; onChange: (value: string) => void }) {
    return (
        <div className="space-y-2">
            <label className="ml-3 text-[11px] font-black uppercase tracking-widest text-slate-400">{label}</label>
            <CityCombobox cities={cities} value={value} onChange={onChange} inputClassName="h-16 w-full rounded-2xl border border-slate-200 bg-slate-50 px-6 font-bold text-slate-700 outline-none transition focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-50" />
        </div>
    );
}

function Star() {
    return <svg className="h-4 w-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 0 0 .95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 0 0-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 0 0-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 0 0-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 0 0 .951-.69l1.07-3.292Z" /></svg>;
}

function HowItWorks() {
    const features = [
        ['1', 'Search a trip', 'Enter your departure, destination, travel date, and seats.'],
        ['2', 'Choose your ride', 'Compare verified drivers, prices, pickup points, and seats.'],
        ['3', 'Book and go', 'Confirm instantly and travel with support close when needed.'],
    ];

    return (
        <section id="how-it-works" className="shell py-12">
            <div className="relative min-h-[650px] overflow-hidden rounded-[3.5rem] px-7 py-12 sm:px-12 lg:min-h-[570px] lg:px-20 lg:py-16">
                <div className="relative z-20 max-w-[410px] lg:pb-10 lg:pr-8">
                    <p className="text-[11px] font-black uppercase tracking-[0.16em] text-[#fb6b55]">How it works</p>
                    <h2 className="mt-4 max-w-[360px] text-[2.9rem] font-black leading-[0.95] text-slate-950 sm:text-[4rem] lg:text-[4.6rem]">Simple process</h2>
                    <p className="mt-5 max-w-[360px] text-sm font-medium leading-7 text-slate-500">Say goodbye to Facebook groups and endless negotiations. BlasaCar connects you in three simple steps.</p>
                    <Link href={path('rides.search')} className="mt-7 inline-flex h-12 items-center justify-center rounded-full bg-[#fb6b55] px-7 text-sm font-black text-white shadow-[0_18px_40px_-18px_rgba(251,107,85,0.75)] transition hover:-translate-y-0.5 hover:bg-[#ef5d49]">Get Started</Link>
                </div>
                <div className="absolute inset-y-12 left-[430px] right-0 hidden lg:block">
                    <svg className="absolute inset-0 z-10 h-full w-full" viewBox="0 0 1000 430" fill="none" preserveAspectRatio="none" aria-hidden="true">
                        <text x="105" y="292" textAnchor="middle" dominantBaseline="central" fontWeight="900" fill="#f1f5f9" style={{ fontSize: '200px', fontFamily: 'sans-serif', opacity: 1 }}>1</text>
                        <text x="445" y="198" textAnchor="middle" dominantBaseline="central" fontWeight="900" fill="#f1f5f9" style={{ fontSize: '200px', fontFamily: 'sans-serif', opacity: 1 }}>2</text>
                        <text x="790" y="108" textAnchor="middle" dominantBaseline="central" fontWeight="900" fill="#f1f5f9" style={{ fontSize: '200px', fontFamily: 'sans-serif', opacity: 1 }}>3</text>

                        <path d="M-80 285 C-4 335 58 330 105 292 C194 168 300 166 445 198 C562 230 654 193 720 128 C748 101 766 108 790 108 C872 108 922 109 1030 99" stroke="#fb6b55" strokeWidth="5" strokeLinecap="round" />
                        <TimelineDot cx="105" cy="292" />
                        <TimelineDot cx="445" cy="198" />
                        <TimelineDot cx="790" cy="108" />
                    </svg>

                    <div className="absolute left-[10.5%] top-[67.9%] z-20 w-[270px]">
                        <div className="relative pt-16">
                            <h3 className="text-sm font-black text-slate-950">Search a trip</h3>
                            <p className="mt-2 text-sm font-medium leading-6 text-slate-500">Enter your departure, destination, travel date, and seats.</p>
                        </div>
                    </div>

                    <div className="absolute left-[44.5%] top-[46%] z-20 w-[285px]">
                        <div className="relative pt-16">
                            <h3 className="text-sm font-black text-slate-950">Choose your ride</h3>
                            <p className="mt-2 text-sm font-medium leading-6 text-slate-500">Compare verified drivers, prices, pickup points, and seats.</p>
                        </div>
                    </div>

                    <div className="absolute left-[79%] top-[25.1%] z-20 w-[285px]">
                        <div className="relative pt-16">
                            <h3 className="text-sm font-black text-slate-950">Book and go</h3>
                            <p className="mt-2 text-sm font-medium leading-6 text-slate-500">Confirm instantly and travel with support close when needed.</p>
                        </div>
                    </div>
                </div>
                <div className="relative z-10 mt-12 grid gap-8 lg:hidden">
                    {features.map(([step, title, copy]) => (
                        <div key={step} className="relative border-l-2 border-[#fb6b55] pl-8">
                            <span className="absolute -left-[11px] top-1 h-5 w-5 rounded-full bg-white shadow-[0_0_0_8px_rgba(15,23,42,0.06)]" />
                            <span className="absolute -left-[5px] top-[10px] h-2 w-2 rounded-full bg-slate-300" />
                            <span className="pointer-events-none absolute right-2 top-[-18px] text-7xl font-black leading-none text-slate-100">{step}</span>
                            <h3 className="text-base font-black text-slate-950">{title}</h3>
                            <p className="mt-2 max-w-[320px] text-sm font-medium leading-6 text-slate-500">{copy}</p>
                        </div>
                    ))}
                </div>
            </div>
        </section>
    );
}

function TimelineDot({ cx, cy }: { cx: string; cy: string }) {
    return (
        <g>
            <circle cx={cx} cy={cy} r="28" fill="#f3f4f6" opacity="0.95" />
            <circle cx={cx} cy={cy} r="14" fill="#ffffff" />
            <circle cx={cx} cy={cy} r="6" fill="#cbd5e1" />
        </g>
    );
}
