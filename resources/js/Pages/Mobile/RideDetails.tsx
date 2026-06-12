import { Link, useForm, usePage } from '@inertiajs/react';
import { FormEvent } from 'react';
import { IconArrowRight, IconCalendar, IconCar, IconClock, IconPin, MobileShell } from '../../components/MobileShell';
import { StatusChip } from '../../components/ui';
import { path } from '../../routes';
import { Ride, SharedProps } from '../../types';

// Shows mobile ride details and the mobile booking request form.
export default function RideDetails({ ride }: { ride: Ride }) {
    const { auth } = usePage<SharedProps>().props;
    const maxSeats = Math.max(1, Math.min(4, ride.available_seats));
    const form = useForm({
        seats: '1',
        redirect_to: path('mobile.trips'),
    });

    // Sends the booking request and asks the backend to redirect back to mobile trips.
    const submit = (event: FormEvent) => {
        event.preventDefault();
        form.post(path('rides.book', ride.id));
    };

    return (
        <MobileShell title="Ride" active="search">
            <div className="space-y-5 px-5 pb-6 pt-5">
                <section className="rounded-lg bg-slate-950 p-5 text-white shadow-xl shadow-slate-900/10">
                    <div className="flex items-start justify-between gap-3">
                        <div className="min-w-0">
                            <p className="text-xs font-black uppercase tracking-wide text-brand-200">Ride details</p>
                            <h1 className="mt-2 text-3xl font-black leading-tight tracking-tight">
                                {ride.departure_city?.name} to {ride.arrival_city?.name}
                            </h1>
                        </div>
                        <StatusChip status={ride.status} />
                    </div>
                    <div className="mt-5 grid grid-cols-2 gap-2 text-xs font-bold">
                        <span className="inline-flex items-center gap-1.5 rounded-lg bg-white/10 px-3 py-2">
                            <IconCalendar />
                            {ride.departure_day_label}
                        </span>
                        <span className="inline-flex items-center gap-1.5 rounded-lg bg-white/10 px-3 py-2">
                            <IconClock />
                            {ride.departure_time_label}
                        </span>
                    </div>
                </section>

                <section className="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                    <h2 className="text-lg font-black text-slate-950">Route</h2>
                    <div className="mt-4 grid grid-cols-[1rem_1fr] gap-x-3">
                        <div className="flex flex-col items-center pt-1">
                            <span className="h-2.5 w-2.5 rounded-full bg-brand-500" />
                            <span className="my-1 h-16 w-px bg-slate-200" />
                            <span className="h-2.5 w-2.5 rounded-full bg-slate-950" />
                        </div>
                        <div className="space-y-5">
                            <div>
                                <p className="text-xs font-black uppercase tracking-wide text-slate-400">Leaving from</p>
                                <p className="text-xl font-black text-slate-950">{ride.departure_city?.name}</p>
                                <p className="mt-1 text-sm font-semibold text-slate-500">{ride.meeting_point ?? 'Meeting point pending'}</p>
                            </div>
                            <div>
                                <p className="text-xs font-black uppercase tracking-wide text-slate-400">Going to</p>
                                <p className="text-xl font-black text-slate-950">{ride.arrival_city?.name}</p>
                                <p className="mt-1 text-sm font-semibold text-slate-500">Direct city-to-city trip</p>
                            </div>
                        </div>
                    </div>
                </section>

                <section className="grid grid-cols-2 gap-2">
                    <Detail icon={<IconCar />} label="Vehicle" value={ride.vehicle ? `${ride.vehicle.brand} ${ride.vehicle.model}` : 'Shared car'} />
                    <Detail icon={<IconPin />} label="Seats left" value={ride.available_seats_label} />
                    <Detail icon={<IconCalendar />} label="Date" value={ride.departure_full_label} />
                    <Detail icon={<IconClock />} label="Price" value={ride.price_label} />
                </section>

                <section className="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                    <p className="text-xs font-black uppercase tracking-wide text-slate-400">Driver</p>
                    <div className="mt-3 flex items-center gap-3">
                        <div className="flex h-12 w-12 items-center justify-center rounded-full bg-slate-950 text-sm font-black text-white">
                            {ride.driver?.initials ?? 'BC'}
                        </div>
                        <div className="min-w-0">
                            <p className="truncate text-base font-black text-slate-950">{ride.driver?.name ?? 'BlasaCar driver'}</p>
                            <p className="mt-0.5 text-xs font-bold text-slate-500">
                                Rating {ride.driver?.profile?.avg_rating ?? '0.0'} - {ride.driver?.profile?.total_trips ?? 0} trips
                            </p>
                        </div>
                    </div>
                    {ride.notes && <p className="mt-4 rounded-lg bg-slate-50 p-3 text-sm font-semibold leading-6 text-slate-600">{ride.notes}</p>}
                </section>

                <section className="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                    <div className="flex items-center justify-between gap-3">
                        <div>
                            <p className="text-xs font-black uppercase tracking-wide text-slate-400">Booking</p>
                            <p className="mt-1 text-2xl font-black text-brand-600">{ride.price_label}</p>
                        </div>
                        <p className="text-right text-xs font-bold text-slate-500">{ride.available_seats_label}</p>
                    </div>

                    {auth.user ? (
                        <form onSubmit={submit} className="mt-4 space-y-3">
                            <label className="block space-y-1.5">
                                <span className="text-[11px] font-black uppercase tracking-wide text-slate-400">Passengers</span>
                                <select
                                    disabled={! ride.can_request || auth.user.role !== 'traveler'}
                                    value={form.data.seats}
                                    onChange={(event) => form.setData('seats', event.target.value)}
                                    className="h-12 w-full rounded-lg border border-slate-200 bg-white px-4 text-sm font-black text-slate-800 outline-none focus:border-brand-400 focus:ring-4 focus:ring-brand-50"
                                >
                                    {Array.from({ length: maxSeats }).map((_, index) => {
                                        const seat = index + 1;
                                        return <option key={seat} value={seat}>{seat} seat{seat > 1 ? 's' : ''}</option>;
                                    })}
                                </select>
                            </label>
                            {form.errors.seats && <p className="text-sm font-semibold text-rose-600">{form.errors.seats}</p>}

                            <button
                                type="submit"
                                disabled={form.processing || ! ride.can_request || auth.user.role !== 'traveler'}
                                className="flex h-12 w-full items-center justify-center gap-2 rounded-lg bg-slate-950 text-sm font-black text-white disabled:bg-slate-200 disabled:text-slate-500"
                            >
                                Request this ride
                                <IconArrowRight />
                            </button>
                        </form>
                    ) : (
                        <Link
                            href={`${path('mobile.login')}?redirect_to=${encodeURIComponent(path('mobile.rides.show', ride.id))}`}
                            className="mt-4 flex h-12 w-full items-center justify-center gap-2 rounded-lg bg-slate-950 text-sm font-black text-white"
                        >
                            Log in to book
                            <IconArrowRight />
                        </Link>
                    )}
                </section>
            </div>
        </MobileShell>
    );
}

// Renders one compact ride fact on mobile details.
function Detail({ icon, label, value }: { icon: React.ReactNode; label: string; value: string }) {
    return (
        <div className="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
            <div className="flex h-9 w-9 items-center justify-center rounded-full bg-brand-50 text-brand-700">{icon}</div>
            <p className="mt-3 text-[11px] font-black uppercase tracking-wide text-slate-400">{label}</p>
            <p className="mt-1 text-sm font-black leading-5 text-slate-950">{value}</p>
        </div>
    );
}
