import { Link, useForm, usePage } from '@inertiajs/react';
import { FormEvent } from 'react';
import { DashboardSidebar, NotificationList, StatTile, StatusChip } from '../../components/ui';
import { Layout } from '../../components/Layout';
import { path } from '../../routes';
import { Booking, Notification, SharedProps, UserSummary } from '../../types';

type TravelerProps = {
    traveler: UserSummary;
    bookings: Booking[];
    upcomingBookings: Booking[];
    notifications: Notification[];
    stats: Record<string, string | number>;
};

const sidebarItems = [
    ['Overview', 'dashboards.traveler', '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="9"/><rect x="14" y="3" width="7" height="5"/><rect x="14" y="12" width="7" height="9"/><rect x="3" y="16" width="7" height="5"/></svg>'],
    ['Search rides', 'rides.search', '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>'],
    ['Become a driver', 'drivers.onboarding.create', '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 17h2l-1.4-4.2A3 3 0 0 0 16.8 11H7.2a3 3 0 0 0-2.8 1.8L3 17h2"/><circle cx="7" cy="17" r="2"/><circle cx="17" cy="17" r="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>'],
].map(([label, route, icon]) => ({ label, route, icon }));

export default function Traveler({ traveler, bookings, upcomingBookings, notifications, stats }: TravelerProps) {
    const nextTrip = upcomingBookings[0];
    const { errors } = usePage<SharedProps>().props;

    return (
        <Layout title="Traveler Dashboard" showFooter={false}>
            <section className="py-10">
                <div className="shell page-enter">
                    <div className="flex gap-8">
                        <DashboardSidebar label="Traveler dashboard" items={sidebarItems} />
                        <div className="min-w-0 flex-1 space-y-6">
                            <div className="surface p-6 sm:p-8">
                                <div className="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                                    <div>
                                        <p className="text-sm font-semibold uppercase tracking-[0.18em] text-brand-600">Traveler workspace</p>
                                        <h1 className="mt-2 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">Hello, {traveler.first_name}.</h1>
                                        <p className="mt-3 max-w-2xl text-slate-500">Manage your trips and booking history from one place.</p>
                                    </div>
                                    <div className="flex flex-wrap gap-3">
                                        <Link href={path('rides.search')} className="brand-button">Find a ride</Link>
                                        <Link href={path('drivers.onboarding.create')} className="brand-button-secondary">Become a driver</Link>
                                    </div>
                                </div>
                            </div>

                            <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                                <StatTile label="Upcoming trips" value={stats.upcoming_trips} />
                                <StatTile label="Completed trips" value={stats.completed_trips} />
                                <StatTile label="Cancelled trips" value={stats.cancelled_trips} />
                                <StatTile label="Avg driver rating" value={stats.avg_driver_rating} />
                            </div>

                            <div className="grid gap-6 xl:grid-cols-[minmax(0,1.25fr)_360px]">
                                <div className="dashboard-panel">
                                    <h2 className="text-xl font-bold text-slate-950">Next confirmed trip</h2>
                                    {nextTrip ? <NextTrip booking={nextTrip} /> : <p className="mt-4 text-sm text-slate-500">No upcoming trips are scheduled for this account yet.</p>}
                                </div>
                                <div className="space-y-6">
                                    <div className="dashboard-panel">
                                        <h2 className="text-xl font-bold text-slate-950">Notifications</h2>
                                        <div className="mt-4 space-y-3"><NotificationList notifications={notifications} /></div>
                                    </div>
                                    <div className="dashboard-panel">
                                        <h2 className="text-xl font-bold text-slate-950">Quick links</h2>
                                        <div className="mt-4 space-y-3">
                                            <Link href={path('rides.search')} className="block rounded-2xl border border-slate-200 px-4 py-3 text-sm font-medium text-slate-700 transition hover:border-brand-200 hover:text-brand-700">Search more rides</Link>
                                            <Link href={path('drivers.onboarding.create')} className="block rounded-2xl border border-slate-200 px-4 py-3 text-sm font-medium text-slate-700 transition hover:border-brand-200 hover:text-brand-700">Become a driver</Link>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div className="dashboard-panel">
                                <div className="flex items-center justify-between">
                                    <h2 className="text-xl font-bold text-slate-950">Booking history</h2>
                                    <p className="text-sm text-slate-500">{bookings.length} booking{bookings.length === 1 ? '' : 's'}</p>
                                </div>
                                {['booking', 'review', 'rating', 'comment'].map((key) => errors[key] && <p key={key} className="mt-4 rounded-2xl bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700">{errors[key]}</p>)}
                                <div className="mt-6 overflow-x-auto rounded-[1.5rem] border border-slate-200">
                                    <table className="min-w-full divide-y divide-slate-200 text-sm">
                                        <thead className="bg-slate-50 text-left text-slate-500">
                                            <tr><th className="px-5 py-4 font-semibold">Route</th><th className="px-5 py-4 font-semibold">Date</th><th className="px-5 py-4 font-semibold">Driver</th><th className="px-5 py-4 font-semibold">Status</th><th className="px-5 py-4 font-semibold">Action</th></tr>
                                        </thead>
                                        <tbody className="divide-y divide-slate-100 bg-white">
                                            {bookings.map((booking) => <TravelerBookingRow key={booking.id} booking={booking} />)}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </Layout>
    );
}

function NextTrip({ booking }: { booking: Booking }) {
    const ride = booking.ride;
    return (
        <div className="mt-6 rounded-[1.75rem] border border-brand-200 bg-brand-50 p-6">
            <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div className="min-w-0">
                    <p className="break-words text-2xl font-bold text-slate-950">{ride?.departure_city?.name} &rarr; {ride?.arrival_city?.name}</p>
                    <p className="mt-2 text-sm text-slate-600">{ride?.departure_datetime_label} &middot; {ride?.meeting_point}</p>
                </div>
                <StatusChip status={booking.status} />
            </div>
            <div className="mt-5 grid gap-3 sm:grid-cols-3">
                <Info label="Driver" value={ride?.driver?.name ?? ''} />
                <Info label="Seats" value={String(booking.seats_reserved)} />
                <Info label="Price" value={ride?.price_label ?? '0 DH'} />
            </div>
        </div>
    );
}

function TravelerBookingRow({ booking }: { booking: Booking }) {
    const cancel = useForm({});
    const review = useForm({ rating: '', comment: '' });
    const ride = booking.ride;
    const submitReview = (event: FormEvent) => {
        event.preventDefault();
        review.post(path('bookings.reviews.store', booking.id));
    };

    return (
        <tr>
            <td className="px-5 py-4 font-medium text-slate-900">{ride?.departure_city?.name} &rarr; {ride?.arrival_city?.name}</td>
            <td className="px-5 py-4 text-slate-600">{ride?.departure_datetime_label}</td>
            <td className="px-5 py-4 text-slate-600">{ride?.driver?.name}</td>
            <td className="px-5 py-4"><StatusChip status={booking.status} /></td>
            <td className="px-5 py-4">
                {booking.can_cancel ? (
                    <button type="button" onClick={() => cancel.patch(path('bookings.cancel', booking.id))} className="rounded-full bg-rose-50 px-4 py-2 text-xs font-bold text-rose-700 transition hover:bg-rose-100">Cancel</button>
                ) : booking.can_review ? (
                    <form onSubmit={submitReview} className="min-w-56 space-y-2">
                        <select required value={review.data.rating} onChange={(event) => review.setData('rating', event.target.value)} className="w-full rounded-2xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 outline-none transition focus:border-brand-400 focus:ring-4 focus:ring-brand-100">
                            <option value="">Rate driver</option>
                            {[5, 4, 3, 2, 1].map((rating) => <option key={rating} value={rating}>{rating}/5</option>)}
                        </select>
                        <textarea value={review.data.comment} onChange={(event) => review.setData('comment', event.target.value)} rows={2} maxLength={1000} placeholder="Comment" className="w-full resize-none rounded-2xl border border-slate-200 bg-white px-3 py-2 text-xs text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-brand-400 focus:ring-4 focus:ring-brand-100" />
                        <button type="submit" className="rounded-full bg-brand-600 px-4 py-2 text-xs font-bold text-white transition hover:bg-brand-700">Submit review</button>
                    </form>
                ) : booking.reviewed ? (
                    <span className="text-xs font-medium text-emerald-600">Reviewed</span>
                ) : (
                    <span className="text-xs font-medium text-slate-400">No action</span>
                )}
            </td>
        </tr>
    );
}

function Info({ label, value }: { label: string; value: string }) {
    return (
        <div className="rounded-2xl bg-white px-4 py-4">
            <p className="text-xs uppercase tracking-[0.14em] text-slate-400">{label}</p>
            <p className="mt-2 font-semibold text-slate-900">{value}</p>
        </div>
    );
}
