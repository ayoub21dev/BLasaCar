import { useMemo, useState } from 'react';
import { City } from '../types';

type CityComboboxProps = {
    cities: City[];
    name?: string;
    value?: number | string | null;
    placeholder?: string;
    inputClassName: string;
    onChange?: (value: string) => void;
};

export function CityCombobox({ cities, name, value, placeholder = 'Select city', inputClassName, onChange }: CityComboboxProps) {
    const [open, setOpen] = useState(false);
    const [query, setQuery] = useState('');
    const selectedValue = value === undefined || value === null ? '' : String(value);
    const selectedCity = cities.find((city) => String(city.id) === selectedValue);
    const visibleCities = useMemo(() => {
        const normalized = query.trim().toLowerCase();

        if (! normalized) {
            return cities;
        }

        return cities.filter((city) => city.name.toLowerCase().includes(normalized));
    }, [cities, query]);

    const selectCity = (city: City) => {
        onChange?.(String(city.id));
        setOpen(false);
        setQuery('');
    };

    return (
        <div className="relative">
            {name && <input type="hidden" name={name} value={selectedValue} />}
            <button
                type="button"
                className={`${inputClassName} flex items-center justify-between gap-3 text-left`}
                aria-haspopup="listbox"
                aria-expanded={open}
                onClick={() => setOpen((current) => ! current)}
            >
                <span className={selectedCity ? '' : 'text-slate-400'}>{selectedCity?.name ?? placeholder}</span>
                <svg className="h-4 w-4 shrink-0 text-slate-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fillRule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clipRule="evenodd" />
                </svg>
            </button>

            {open && (
                <div className="absolute left-0 right-0 top-[calc(100%+0.5rem)] z-50 overflow-hidden rounded-[1.25rem] border border-slate-200 bg-white shadow-[0_24px_70px_-28px_rgba(15,23,42,0.45)]">
                    <div className="border-b border-slate-100 p-2">
                        <input
                            type="search"
                            className="w-full rounded-xl bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 outline-none focus:bg-white focus:ring-4 focus:ring-brand-50"
                            placeholder="Search city"
                            autoComplete="off"
                            value={query}
                            onChange={(event) => setQuery(event.target.value)}
                        />
                    </div>
                    <div className="max-h-64 overflow-y-auto p-2" role="listbox">
                        {visibleCities.map((city) => (
                            <button
                                key={city.id}
                                type="button"
                                className="flex w-full items-center rounded-xl px-4 py-2.5 text-left text-sm font-semibold text-slate-700 transition hover:bg-brand-50 hover:text-brand-700 data-[selected=true]:bg-brand-50 data-[selected=true]:text-brand-700"
                                data-selected={String(city.id) === selectedValue}
                                role="option"
                                aria-selected={String(city.id) === selectedValue}
                                onClick={() => selectCity(city)}
                            >
                                {city.name}
                            </button>
                        ))}
                    </div>
                    {visibleCities.length === 0 && <p className="px-4 py-3 text-sm font-semibold text-slate-400">No city found</p>}
                </div>
            )}
        </div>
    );
}
