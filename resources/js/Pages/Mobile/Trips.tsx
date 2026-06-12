import { Link, useForm, usePage } from '@inertiajs/react';
import { AuthPrompt, IconArrowRight, IconCalendar, IconClock, IconTicket, MobileShell } from '../../components/MobileShell';
import { StatusChip } from '../../components/ui';
import { path } from '../../routes';
import { Booking, SharedProps } from '../../types';

type TripsProps = {
    bookings: Booking[];
    upcomingBookings: Booking[];
};

// Shows mobile booking history, next trip, and login prompt when needed.
export default function Trips({ bookings, upcomingBookings }: TripsProps) {
    const { auth } = usePage<SharedProps>().props;
    const nextBooking = upcomingBookings[0];

    if (! auth.user) {
        return (
            <MobileShell title="Trips" active="trips">
                <AuthPrompt title="Log in to see trips" message="Your bookings, trip contacts, and ride updates live here." />
            </MobileShell>
        );
    }

    return (
        <MobileShell title="Trips" active="trips">
            <div className="space-y-5 px-5 pb-6 pt-5">
                <section>
                    <h1 className="text-3xl font-black tracking-tight text-slate-950">My trips</h1>
                    <p className="mt-2 text-sm font-semibold text-slate-500">{bookings.length} booking{bookings.length === 1 ? '' : 's'} in your account</p>
                </section>

                {nextBooking?.ride ? (
                    <section className="rounded-lg bg-slate-950 p-5 text-white shadow-xl shadow-slate-900/10">
                        <p className="text-xs font-black uppercase tracking-wide text-brand-200">Next trip</p>
                        <h2 className="mt-2 text-2xl font-black">{nextBooking.ride.departure_city?.name} to {nextBooking.ride.arrival_city?.name}</h2>
                        <div className="mt-4 grid grid-cols-2 gap-2 text-xs font-bold">
                            <span className="inline-flex items-center gap-1.5 rounded-lg bg-white/10 px-3 py-2">
                                <IconCalendar />
                                {nextBooking.ride.departure_day_label}
                            </span>
                            <span className="inline-flex items-center gap-1.5 rounded-lg bg-white/10 px-3 py-2">
                                <IconClock />
                                {nextBooking.ride.departure_time_label}
                            </span>
                        </div>
                        <Link href={path('mobile.rides.show', nextBooking.ride.id)} className="mt-4 flex h-11 items-center justify-center gap-2 rounded-lg bg-white text-sm font-black text-slate-950">
                            Open trip
                            <IconArrowRight />
                        </Link>
                    </section>
                ) : (
                    <section className="rounded-lg border border-dashed border-slate-300 bg-white p-5 text-center">
                        <div className="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-brand-50 text-brand-700">
                            <IconTicket />
                        </div>
                        <p className="mt-3 font-black text-slate-900">No upcoming trip</p>
                        <Link href={path('mobile.search')} className="mt-3 inline-flex h-10 items-center justify-center rounded-lg bg-slate-950 px-4 text-sm font-black text-white">
                            Find a ride
                        </Link>
                    </section>
                )}

                <section className="space-y-3">
                    <h2 className="text-lg font-black text-slate-950">All bookings</h2>
                    {bookings.length > 0 ? (
                        bookings.map((booking) => <BookingCard key={booking.id} booking={booking} />)
                    ) : (
                        <p className="rounded-lg border border-slate-200 bg-white p-5 text-sm font-semibold text-slate-500 shadow-sm">No bookings yet.</p>
                    )}
                </section>
            </div>
        </MobileShell>
    );
}

// Renders one mobile booking card with details and cancellation action.
function BookingCard({ booking }: { booking: Booking }) {
    const cancel = useForm({});
    const ride = booking.ride;

    return (
        <article className="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
            <div className="flex items-start justify-between gap-3">
                <div className="min-w-0">
                    <p className="truncate text-base font-black text-slate-950">
                        {ride?.departure_city?.name ?? 'Departure'} to {ride?.arrival_city?.name ?? 'Arrival'}
                    </p>
                    <p className="mt-1 text-xs font-bold text-slate-500">
                        {booking.seats_reserved} seat{booking.seats_reserved === 1 ? '' : 's'} - {ride?.price_label ?? 'Price pending'}
                    </p>
                </div>
                <StatusChip status={booking.status} />
            </div>

            {ride && (
                <div className="mt-4 grid grid-cols-2 gap-2 text-xs font-bold text-slate-600">
                    <span className="inline-flex items-center gap-1.5 rounded-lg bg-slate-50 px-3 py-2">
                        <IconCalendar />
                        {ride.departure_day_label}
                    </span>
                    <span className="inline-flex items-center gap-1.5 rounded-lg bg-slate-50 px-3 py-2">
                        <IconClock />
                        {ride.departure_time_label}
                    </span>
                </div>
            )}

            {booking.can_view_contact && booking.driver_contact && (
                <div className="mt-4 rounded-lg bg-emerald-50 p-3 text-sm font-bold text-emerald-800">
                    Driver contact: {booking.driver_contact.phone || booking.driver_contact.name}
                </div>
            )}

            <div className="mt-4 grid grid-cols-2 gap-2">
                {ride ? (
                    <Link href={path('mobile.rides.show', ride.id)} className="flex h-10 items-center justify-center rounded-lg bg-slate-950 text-sm font-black text-white">
                        Details
                    </Link>
                ) : (
                    <span />
                )}
                {booking.can_cancel ? (
                    <button
                        type="button"
                        onClick={() => cancel.patch(path('bookings.cancel', booking.id))}
                        disabled={cancel.processing}
                        className="flex h-10 items-center justify-center rounded-lg bg-rose-50 text-sm font-black text-rose-700 disabled:opacity-60"
                    >
                        Cancel
                    </button>
                ) : (
                    <span className="flex h-10 items-center justify-center rounded-lg bg-slate-50 text-sm font-black text-slate-500">Saved</span>
                )}
            </div>
        </article>
    );
}
