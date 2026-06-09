import { Link } from '@inertiajs/react';
import { path } from '../routes';
import { Ride } from '../types';
import { IconArrowRight, IconCalendar, IconClock, IconPin } from './MobileShell';

type MobileRideCardProps = {
    ride: Ride;
    ctaLabel?: string;
};

export function MobileRideCard({ ride, ctaLabel = 'View' }: MobileRideCardProps) {
    const driver = ride.driver;
    const driverInitials = driver?.initials ?? driver?.first_name?.slice(0, 1).toUpperCase() ?? 'BC';
    const vehicleLabel = ride.vehicle ? `${ride.vehicle.brand} ${ride.vehicle.model}` : 'Shared car';

    return (
        <article className="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
            <Link href={path('mobile.rides.show', ride.id)} className="block">
                <div className="flex items-start justify-between gap-3">
                    <div className="flex min-w-0 items-center gap-3">
                        <div className="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-slate-950 text-xs font-black text-white">
                            {driverInitials}
                        </div>
                        <div className="min-w-0">
                            <p className="truncate text-sm font-black text-slate-950">{driver?.name ?? 'BlasaCar driver'}</p>
                            <p className="mt-0.5 truncate text-xs font-semibold text-slate-500">
                                {driver?.profile?.avg_rating ?? 'New'} rating - {vehicleLabel}
                            </p>
                        </div>
                    </div>
                    <span className="shrink-0 text-lg font-black text-brand-600">{ride.price_label}</span>
                </div>

                <div className="mt-4 grid grid-cols-[1rem_1fr] gap-x-3 gap-y-1">
                    <div className="flex flex-col items-center pt-1">
                        <span className="h-2.5 w-2.5 rounded-full bg-brand-500" />
                        <span className="my-1 h-10 w-px bg-slate-200" />
                        <span className="h-2.5 w-2.5 rounded-full bg-slate-950" />
                    </div>
                    <div className="space-y-3">
                        <div>
                            <p className="text-xs font-black uppercase tracking-wide text-slate-400">From</p>
                            <p className="text-base font-black text-slate-950">{ride.departure_city?.name ?? 'Departure city'}</p>
                        </div>
                        <div>
                            <p className="text-xs font-black uppercase tracking-wide text-slate-400">To</p>
                            <p className="text-base font-black text-slate-950">{ride.arrival_city?.name ?? 'Arrival city'}</p>
                        </div>
                    </div>
                </div>

                <div className="mt-4 grid grid-cols-2 gap-2 text-xs font-bold text-slate-600">
                    <span className="inline-flex items-center gap-1.5 rounded-lg bg-slate-50 px-3 py-2">
                        <IconCalendar />
                        {ride.departure_day_label}
                    </span>
                    <span className="inline-flex items-center gap-1.5 rounded-lg bg-slate-50 px-3 py-2">
                        <IconClock />
                        {ride.departure_time_label}
                    </span>
                    <span className="inline-flex items-center gap-1.5 rounded-lg bg-slate-50 px-3 py-2">
                        <IconPin />
                        {ride.available_seats_label}
                    </span>
                    <span className="inline-flex items-center justify-center gap-1.5 rounded-lg bg-slate-950 px-3 py-2 font-black text-white">
                        {ctaLabel}
                        <IconArrowRight />
                    </span>
                </div>
            </Link>
        </article>
    );
}
