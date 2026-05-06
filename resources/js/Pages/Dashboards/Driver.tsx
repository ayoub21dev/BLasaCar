import { Link, useForm, usePage } from '@inertiajs/react';
import { DashboardSidebar, NotificationList, StatTile, StatusChip } from '../../components/ui';
import { Layout } from '../../components/Layout';
import { path } from '../../routes';
import { Booking, Notification, Ride, SharedProps, UserSummary } from '../../types';

type DriverProps = {
    driver: UserSummary;
    rides: Ride[];
    bookings: Booking[];
    notifications: Notification[];
    stats: Record<string, string | number>;
    weeklySeatSales: Array<{ label: string; seats: number; height: number }>;
};

const sidebarItems = [
    ['Overview', 'dashboards.driver', '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="9"/><rect x="14" y="3" width="7" height="5"/><rect x="14" y="12" width="7" height="9"/><rect x="3" y="16" width="7" height="5"/></svg>'],
    ['Search rides', 'rides.search', '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>'],
].map(([label, route, icon]) => ({ label, route, icon }));

export default function Driver({ driver, rides, bookings, notifications, stats, weeklySeatSales }: DriverProps) {
    const { errors } = usePage<SharedProps>().props;

    return (
        <Layout title="Driver Dashboard" showFooter={false}>
            <section className="py-10">
                <div className="shell page-enter">
                    <div className="flex gap-8">
                        <DashboardSidebar label="Driver dashboard" items={sidebarItems} />
                        <div className="min-w-0 flex-1 space-y-6">
                            <div className="surface p-6 sm:p-8">
                                <div className="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                                    <div>
                                        <p className="text-sm font-semibold uppercase tracking-[0.18em] text-brand-600">Driver workspace</p>
                                        <h1 className="mt-2 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">Welcome back, {driver.first_name}.</h1>
                                        <p className="mt-3 max-w-2xl text-slate-500">Track your rides, seats, and booking requests.</p>
                                    </div>
                                    <Link href={path('rides.publish')} className="brand-button">Publish new ride</Link>
                                </div>
                            </div>

                            <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                                <StatTile label="Published rides" value={stats.published_rides} />
                                <StatTile label="Upcoming rides" value={stats.upcoming_rides} />
                                <StatTile label="Completion rate" value={`${stats.completion_rate}%`} />
                                <StatTile label="Handled bookings" value={`${stats.response_rate}%`} />
                            </div>

                            <div className="grid gap-6 xl:grid-cols-[minmax(0,1.3fr)_360px]">
                                <div className="dashboard-panel">
                                    <div className="flex items-center justify-between">
                                        <h2 className="text-xl font-bold text-slate-950">My rides</h2>
                                        <Link href={path('rides.search')} className="text-sm font-medium text-brand-700">View public search</Link>
                                    </div>
                                    {errors.ride && <p className="mt-4 rounded-2xl bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700">{errors.ride}</p>}
                                    <div className="mt-6 space-y-4">
                                        {rides.slice(0, 5).length > 0 ? rides.slice(0, 5).map((ride) => <DriverRideRow key={ride.id} ride={ride} />) : <p className="text-sm text-slate-500">No rides published yet.</p>}
                                    </div>
                                </div>
                                <div className="space-y-6">
                                    <div className="dashboard-panel">
                                        <h2 className="text-xl font-bold text-slate-950">Notifications</h2>
                                        <div className="mt-4 space-y-3"><NotificationList notifications={notifications} /></div>
                                    </div>
                                    <div className="dashboard-panel">
                                        <h2 className="text-xl font-bold text-slate-950">Weekly seats sold</h2>
                                        <p className="mt-2 text-sm text-slate-500">Aggregated from your recent booking activity.</p>
                                        <div className="mt-6 flex h-52 items-end gap-3">
                                            {weeklySeatSales.map((day) => (
                                                <div key={day.label} className="flex flex-1 flex-col items-center gap-3">
                                                    <div className="w-full rounded-t-2xl bg-gradient-to-t from-brand-600 to-brand-300" style={{ height: `${day.height}%` }} />
                                                    <div className="text-center text-xs text-slate-500"><div className="font-semibold text-slate-700">{day.seats}</div><div>{day.label}</div></div>
                                                </div>
                                            ))}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div className="dashboard-panel">
                                <div className="flex items-center justify-between">
                                    <h2 className="text-xl font-bold text-slate-950">Recent booking requests</h2>
                                    <p className="text-sm text-slate-500">{bookings.length} total</p>
                                </div>
                                {errors.booking && <p className="mt-4 rounded-2xl bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700">{errors.booking}</p>}
                                <div className="mt-6 overflow-x-auto rounded-[1.5rem] border border-slate-200">
                                    <table className="min-w-full divide-y divide-slate-200 text-sm">
                                        <thead className="bg-slate-50 text-left text-slate-500"><tr><th className="px-5 py-4 font-semibold">Traveler</th><th className="px-5 py-4 font-semibold">Route</th><th className="px-5 py-4 font-semibold">Seats</th><th className="px-5 py-4 font-semibold">Status</th><th className="px-5 py-4 font-semibold">Action</th></tr></thead>
                                        <tbody className="divide-y divide-slate-100 bg-white">{bookings.slice(0, 6).map((booking) => <DriverBookingRow key={booking.id} booking={booking} />)}</tbody>
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

function DriverRideRow({ ride }: { ride: Ride }) {
    const form = useForm({});
    return (
        <div className="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-5">
            <div className="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p className="font-semibold text-slate-900">{ride.departure_city?.name} &rarr; {ride.arrival_city?.name}</p>
                    <p className="mt-1 text-sm text-slate-500">{ride.departure_datetime_label} &middot; {ride.available_seats} seats left</p>
                </div>
                <div className="flex flex-wrap items-center gap-2">
                    <StatusChip status={ride.status} />
                    {ride.can_complete && <button type="button" onClick={() => form.patch(path('rides.complete', ride.id))} className="rounded-full bg-emerald-600 px-4 py-2 text-xs font-bold text-white transition hover:bg-emerald-700">Complete</button>}
                </div>
            </div>
        </div>
    );
}

function DriverBookingRow({ booking }: { booking: Booking }) {
    const confirm = useForm({});
    const reject = useForm({});
    return (
        <tr>
            <td className="px-5 py-4 font-medium text-slate-900">{booking.traveler?.name}</td>
            <td className="px-5 py-4 text-slate-600">{booking.ride?.departure_city?.name} &rarr; {booking.ride?.arrival_city?.name}</td>
            <td className="px-5 py-4 text-slate-600">{booking.seats_reserved}</td>
            <td className="px-5 py-4"><StatusChip status={booking.status} /></td>
            <td className="px-5 py-4">
                {booking.status === 'pending' ? (
                    <div className="flex flex-wrap gap-2">
                        <button type="button" onClick={() => confirm.patch(path('bookings.confirm', booking.id))} className="rounded-full bg-emerald-600 px-4 py-2 text-xs font-bold text-white transition hover:bg-emerald-700">Accept</button>
                        <button type="button" onClick={() => reject.patch(path('bookings.reject', booking.id))} className="rounded-full bg-rose-50 px-4 py-2 text-xs font-bold text-rose-700 transition hover:bg-rose-100">Reject</button>
                    </div>
                ) : <span className="text-xs font-medium text-slate-400">No action</span>}
            </td>
        </tr>
    );
}
