import { Link } from '@inertiajs/react';
import { IconArrowRight, IconSearch, MobileShell } from '../../components/MobileShell';
import { MobileRideCard } from '../../components/MobileRideCard';
import { MobileSearchFilterValues, MobileSearchForm } from '../../components/MobileSearchForm';
import { path } from '../../routes';
import { City, Ride } from '../../types';

type SearchProps = {
    cities: City[];
    rides: Ride[];
    filters: MobileSearchFilterValues;
};

export default function Search({ cities, rides, filters }: SearchProps) {
    return (
        <MobileShell title="Search" active="search">
            <div className="space-y-5 px-5 pb-6 pt-5">
                <section>
                    <h1 className="text-3xl font-black tracking-tight text-slate-950">Search rides</h1>
                    <p className="mt-2 text-sm font-semibold leading-6 text-slate-500">
                        Pick your cities and date, then choose a driver.
                    </p>
                </section>

                <MobileSearchForm cities={cities} initialFilters={filters} buttonLabel="Update results" preserveState />

                <section>
                    <div className="mb-3 flex items-center justify-between">
                        <div>
                            <h2 className="text-lg font-black text-slate-950">Results</h2>
                            <p className="text-xs font-bold text-slate-500">{rides.length} ride{rides.length === 1 ? '' : 's'} available</p>
                        </div>
                        <Link href={path('mobile.home')} className="inline-flex items-center gap-1 text-xs font-black text-brand-700">
                            Home
                            <IconArrowRight />
                        </Link>
                    </div>

                    <div className="space-y-3">
                        {rides.length > 0 ? (
                            rides.map((ride) => <MobileRideCard key={ride.id} ride={ride} ctaLabel="Book" />)
                        ) : (
                            <div className="rounded-lg border border-slate-200 bg-white p-6 text-center shadow-sm">
                                <div className="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-brand-50 text-brand-700">
                                    <IconSearch />
                                </div>
                                <p className="mt-4 font-black text-slate-900">No matching rides</p>
                                <p className="mt-1 text-sm font-semibold leading-6 text-slate-500">Try another city, date, or fewer seats.</p>
                            </div>
                        )}
                    </div>
                </section>
            </div>
        </MobileShell>
    );
}
