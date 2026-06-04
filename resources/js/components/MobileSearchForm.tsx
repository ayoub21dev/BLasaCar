import { router } from '@inertiajs/react';
import { FormEvent, useState } from 'react';
import { CityCombobox } from './CityCombobox';
import { IconCalendar, IconPin, IconSearch } from './MobileShell';
import { path } from '../routes';
import { City } from '../types';

export type MobileSearchFilterValues = {
    departure_city_id?: number | string | null;
    arrival_city_id?: number | string | null;
    departure_date?: string | null;
    seats?: number | string | null;
};

type MobileSearchFormProps = {
    cities: City[];
    initialFilters?: MobileSearchFilterValues;
    defaultDate?: string;
    buttonLabel?: string;
    preserveState?: boolean;
};

const inputClassName = 'h-12 w-full rounded-lg border border-slate-200 bg-white px-4 text-sm font-black text-slate-800 outline-none transition focus:border-brand-400 focus:ring-4 focus:ring-brand-50';

export function MobileSearchForm({ cities, initialFilters = {}, defaultDate = '', buttonLabel = 'Search rides', preserveState = false }: MobileSearchFormProps) {
    const [filters, setFilters] = useState({
        departure_city_id: initialFilters.departure_city_id ? String(initialFilters.departure_city_id) : '',
        arrival_city_id: initialFilters.arrival_city_id ? String(initialFilters.arrival_city_id) : '',
        departure_date: initialFilters.departure_date ?? defaultDate,
        seats: initialFilters.seats ? String(initialFilters.seats) : '1',
    });

    const submit = (event: FormEvent) => {
        event.preventDefault();
        router.get(path('mobile.search'), filters, {
            preserveScroll: true,
            preserveState,
        });
    };

    return (
        <form onSubmit={submit} className="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
            <div className="space-y-3">
                <SearchField label="Leaving from">
                    <CityCombobox
                        cities={cities}
                        value={filters.departure_city_id}
                        onChange={(value) => setFilters((current) => ({ ...current, departure_city_id: value }))}
                        inputClassName={inputClassName}
                        placeholder="Choose city"
                    />
                </SearchField>

                <SearchField label="Going to">
                    <CityCombobox
                        cities={cities}
                        value={filters.arrival_city_id}
                        onChange={(value) => setFilters((current) => ({ ...current, arrival_city_id: value }))}
                        inputClassName={inputClassName}
                        placeholder="Choose city"
                    />
                </SearchField>

                <div className="grid grid-cols-[1fr_7rem] gap-3">
                    <SearchField label="Date">
                        <div className="relative">
                            <IconCalendar className="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                            <input
                                type="date"
                                value={filters.departure_date ?? ''}
                                onChange={(event) => setFilters((current) => ({ ...current, departure_date: event.target.value }))}
                                className={`${inputClassName} pl-10`}
                            />
                        </div>
                    </SearchField>
                    <SearchField label="Seats">
                        <div className="relative">
                            <IconPin className="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                            <select
                                value={filters.seats}
                                onChange={(event) => setFilters((current) => ({ ...current, seats: event.target.value }))}
                                className={`${inputClassName} pl-9`}
                            >
                                {[1, 2, 3, 4].map((seat) => (
                                    <option key={seat} value={seat}>{seat}</option>
                                ))}
                            </select>
                        </div>
                    </SearchField>
                </div>
            </div>

            <button type="submit" className="mt-4 flex h-12 w-full items-center justify-center gap-2 rounded-lg bg-slate-950 text-sm font-black text-white shadow-lg shadow-slate-900/10 active:scale-[0.99]">
                <IconSearch />
                {buttonLabel}
            </button>
        </form>
    );
}

function SearchField({ label, children }: { label: string; children: React.ReactNode }) {
    return (
        <label className="block space-y-1.5">
            <span className="text-[11px] font-black uppercase tracking-wide text-slate-400">{label}</span>
            {children}
        </label>
    );
}
