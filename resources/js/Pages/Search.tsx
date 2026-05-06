import { router } from '@inertiajs/react';
import { FormEvent, useState } from 'react';
import { CityCombobox } from '../components/CityCombobox';
import { Layout } from '../components/Layout';
import { RideCard } from '../components/RideCard';
import { path } from '../routes';
import { City, Ride } from '../types';

type SearchProps = {
    cities: City[];
    rides: Ride[];
    filters: {
        departure_city_id?: string | number | null;
        arrival_city_id?: string | number | null;
        departure_date?: string | null;
        seats?: string | number | null;
    };
};

export default function Search({ cities, rides, filters }: SearchProps) {
    const [data, setData] = useState({
        departure_city_id: filters.departure_city_id ? String(filters.departure_city_id) : '',
        arrival_city_id: filters.arrival_city_id ? String(filters.arrival_city_id) : '',
        departure_date: filters.departure_date ?? '',
        seats: filters.seats ? String(filters.seats) : '1',
    });

    const submit = (event: FormEvent) => {
        event.preventDefault();
        router.get(path('rides.search'), data);
    };

    return (
        <Layout title="Find a route that fits your next trip.">
            <section className="py-12">
                <div className="shell page-enter">
                    <div className="relative overflow-hidden rounded-[3.5rem] border border-slate-100 bg-white p-8 shadow-sm sm:p-12 lg:p-16">
                        <div className="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                            <div>
                                <div className="mb-6 inline-flex items-center gap-2 rounded-full bg-brand-100 px-4 py-2 text-[12px] font-black uppercase tracking-widest text-brand-600">Search Rides</div>
                                <h1 className="text-[3rem] font-black leading-[0.95] tracking-tight text-slate-900 sm:text-[4rem]">
                                    Find a route for <br /> your next <span className="font-serif italic text-brand-500">trip</span>.
                                </h1>
                            </div>
                            <p className="rounded-full bg-slate-50 px-6 py-3 text-lg font-medium text-slate-500">
                                {rides.length} ride{rides.length === 1 ? '' : 's'} available
                            </p>
                        </div>

                        <form onSubmit={submit} className="mt-6 grid gap-3 lg:grid-cols-[1fr_1fr_1fr_150px_160px]">
                            <div className="input-shell">
                                <LocationIcon />
                                <div className="min-w-0 flex-1">
                                    <CityCombobox cities={cities} value={data.departure_city_id} onChange={(value) => setData({ ...data, departure_city_id: value })} placeholder="Leaving from" inputClassName="w-full bg-transparent text-sm font-medium text-slate-700 outline-none" />
                                </div>
                            </div>
                            <div className="input-shell">
                                <LocationIcon />
                                <div className="min-w-0 flex-1">
                                    <CityCombobox cities={cities} value={data.arrival_city_id} onChange={(value) => setData({ ...data, arrival_city_id: value })} placeholder="Going to" inputClassName="w-full bg-transparent text-sm font-medium text-slate-700 outline-none" />
                                </div>
                            </div>
                            <label className="input-shell cursor-pointer">
                                <svg className="h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2" /><line x1="16" x2="16" y1="2" y2="6" /><line x1="8" x2="8" y1="2" y2="6" /><line x1="3" x2="21" y1="10" y2="10" /></svg>
                                <input type="date" value={data.departure_date} onChange={(event) => setData({ ...data, departure_date: event.target.value })} className="date-input-clean w-full cursor-pointer bg-transparent text-sm font-medium text-slate-700 outline-none" />
                            </label>
                            <label className="input-shell">
                                <svg className="h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" /><circle cx="12" cy="7" r="4" /></svg>
                                <select value={data.seats} onChange={(event) => setData({ ...data, seats: event.target.value })} className="w-full bg-transparent text-sm font-medium text-slate-700 outline-none">
                                    {[1, 2, 3, 4].map((seat) => <option key={seat} value={seat}>{seat} seat{seat > 1 ? 's' : ''}</option>)}
                                </select>
                            </label>
                            <button type="submit" className="brand-button rounded-[1.4rem]">Update search</button>
                        </form>
                    </div>

                    <div className="mt-10">
                        <div className="space-y-6">
                            {rides.length > 0 ? rides.map((ride) => <RideCard key={ride.id} ride={ride} />) : (
                                <div className="surface-soft p-10 text-center">
                                    <h2 className="text-2xl font-bold text-slate-900">No rides matched those filters.</h2>
                                    <p className="mx-auto mt-3 max-w-md text-slate-500">Try changing the departure city, destination, date, or seat count.</p>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </section>
        </Layout>
    );
}

function LocationIcon() {
    return <svg className="h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" /><circle cx="12" cy="10" r="3" /></svg>;
}
