import { Link, useForm, usePage } from '@inertiajs/react';
import { ReactNode } from 'react';
import { Layout } from '../../components/Layout';
import { StatusChip } from '../../components/ui';
import { asset, path } from '../../routes';
import { Booking, BookingContact, Notification, Ride, SharedProps, UserSummary } from '../../types';

type DriverProps = {
    driver: UserSummary;
    rides: Ride[];
    bookings: Booking[];
    notifications: Notification[];
    stats: Record<string, string | number>;

};

type IconProps = {
    className?: string;
};

const statCards = [
    { key: 'published_rides', label: 'Published rides', note: 'All listed rides', icon: 'car' },
    { key: 'upcoming_rides', label: 'Upcoming rides', note: 'Scheduled departures', icon: 'calendar' },
    { key: 'completion_rate', label: 'Completion rate', note: 'Finished rides', icon: 'check', suffix: '%' },
    { key: 'response_rate', label: 'Handled bookings', note: 'Requests answered', icon: 'bell', suffix: '%' },
];

function IconHome({ className = 'h-5 w-5' }: IconProps) {
    return <svg className={className} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round"><path d="m3 11 9-8 9 8" /><path d="M5 10v10h14V10" /><path d="M9 20v-6h6v6" /></svg>;
}

function IconSearch({ className = 'h-5 w-5' }: IconProps) {
    return <svg className={className} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round"><circle cx="11" cy="11" r="8" /><path d="m21 21-4.3-4.3" /></svg>;
}

function IconCar({ className = 'h-5 w-5' }: IconProps) {
    return <svg className={className} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round"><path d="M19 17h2l-1.4-4.2A3 3 0 0 0 16.8 11H7.2a3 3 0 0 0-2.8 1.8L3 17h2" /><circle cx="7" cy="17" r="2" /><circle cx="17" cy="17" r="2" /><path d="M8 11l1.4-4h5.2L16 11" /></svg>;
}

function IconCalendar({ className = 'h-5 w-5' }: IconProps) {
    return <svg className={className} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round"><rect x="3" y="4" width="18" height="18" rx="2" /><path d="M16 2v4M8 2v4M3 10h18" /></svg>;
}

function IconCheck({ className = 'h-5 w-5' }: IconProps) {
    return <svg className={className} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.3" strokeLinecap="round"><path d="m5 12 5 5L20 7" /></svg>;
}

function IconBell({ className = 'h-5 w-5' }: IconProps) {
    return <svg className={className} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9" /><path d="M13.7 21a2 2 0 0 1-3.4 0" /></svg>;
}

function IconArrow({ className = 'h-4 w-4' }: IconProps) {
    return <svg className={className} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round"><path d="M5 12h14M13 5l7 7-7 7" /></svg>;
}



function IconPlus({ className = 'h-5 w-5' }: IconProps) {
    return <svg className={className} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.3" strokeLinecap="round"><path d="M12 5v14M5 12h14" /></svg>;
}

export default function Driver({ driver, rides, bookings, notifications, stats }: DriverProps) {
    const { errors } = usePage<SharedProps>().props;
    const unreadNotifications = notifications.filter((notification) => ! notification.is_read).length;

    return (
        <Layout title="Driver Dashboard" showHeader={false} showFooter={false}>
            <section className="min-h-screen bg-slate-50 text-slate-950">
                <div className="grid min-h-screen lg:grid-cols-[280px_minmax(0,1fr)]">
                    <DriverSidebar />

                    <main className="min-w-0 px-4 py-5 sm:px-6 lg:px-8">
                        <TopBar driver={driver} unreadNotifications={unreadNotifications} />

                        <div className="mx-auto mt-6 max-w-[1320px] space-y-6">
                            <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                                {statCards.map((card) => <StatCard key={card.key} card={card} value={stats[card.key] ?? '0'} />)}
                            </div>

                            <div className="grid gap-6 xl:grid-cols-[minmax(0,1fr)_390px]">
                                <div className="min-w-0 space-y-6">
                                    <RidesPanel rides={rides} error={errors.ride} />
                                </div>
                                <aside className="min-w-0 space-y-5">
                                    <NotificationsPanel notifications={notifications} />

                                </aside>
                            </div>

                            <BookingRequests bookings={bookings} error={errors.booking} />
                        </div>
                    </main>
                </div>
            </section>
        </Layout>
    );
}

function DriverSidebar() {
    const logout = useForm({});

    return (
        <aside className="hidden border-r border-slate-200 bg-white px-4 py-6 lg:block">
            <div className="sticky top-6 flex h-[calc(100vh-3rem)] flex-col">
                <Link href={path('home')} className="flex items-center gap-3 px-3 py-2">
                    <img src={asset('assets/logoBlasaCar.png')} alt="BlasaCar" className="h-9 w-auto" />
                    <span className="text-xl font-semibold tracking-tight text-slate-950">Blasa<span className="text-brand-600">Car</span></span>
                </Link>

                <div className="mt-8 px-3">
                    <p className="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Driver</p>
                </div>
                <nav className="mt-3 space-y-1">
                    <SideLink href={path('dashboards.driver')} active icon={<IconHome />}>Overview</SideLink>
                    <SideLink href={path('rides.publish')} icon={<IconPlus />}>Publish ride</SideLink>
                    <SideLink href={path('rides.search')} icon={<IconSearch />}>Search rides</SideLink>
                    <SideLink href="#booking-requests" icon={<IconCalendar />}>Booking requests</SideLink>
                    <button
                        type="button"
                        onClick={() => logout.post(path('logout'))}
                        className="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-rose-600 transition hover:bg-rose-50 cursor-pointer"
                    >
                        <span className="flex h-5 w-5 items-center justify-center">
                            <svg className="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" /><path d="m16 17 5-5-5-5" /><path d="M21 12H9" /></svg>
                        </span>
                        Log out
                    </button>
                </nav>

                <div className="mt-auto rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <h3 className="text-sm font-semibold text-slate-950">Ready to fill seats?</h3>
                    <p className="mt-2 text-sm leading-6 text-slate-500">Publish a route and manage requests from this dashboard.</p>
                    <Link href={path('rides.publish')} className="mt-4 inline-flex w-full items-center justify-center rounded-lg bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white">Publish ride</Link>
                </div>
            </div>
        </aside>
    );
}

function SideLink({ href, active = false, icon, children }: { href: string; active?: boolean; icon: ReactNode; children: ReactNode }) {
    return (
        <Link href={href} className={`flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium ${active ? 'bg-slate-950 text-white' : 'text-slate-600'}`}>
            <span className="flex h-5 w-5 items-center justify-center">{icon}</span>
            {children}
        </Link>
    );
}

function TopBar({ driver, unreadNotifications }: { driver: UserSummary; unreadNotifications: number }) {
    return (
        <header className="mx-auto flex max-w-[1320px] flex-col gap-3 rounded-2xl border border-slate-200 bg-white p-3 md:flex-row md:items-center md:justify-between">
            <div className="flex items-center gap-3 px-2 lg:hidden">
                <img src={asset('assets/logoBlasaCar.png')} alt="BlasaCar" className="h-9 w-auto" />
                <span className="text-xl font-semibold tracking-tight">Blasa<span className="text-brand-600">Car</span></span>
            </div>

            <div className="flex items-center justify-between gap-4 px-1 md:ml-auto md:justify-end">
                <a href="#notifications" className="relative flex h-11 w-11 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-600 transition hover:border-brand-200 hover:text-brand-700" aria-label="Open notifications">
                    <IconBell />
                    {unreadNotifications > 0 && <span className="absolute -right-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-brand-600 px-1 text-[10px] font-black text-white">{unreadNotifications}</span>}
                </a>
                <div className="flex items-center gap-3">
                    {driver.profile_photo_url ? (
                        <img src={driver.profile_photo_url} alt={driver.name} className="h-10 w-10 rounded-full object-cover" />
                    ) : (
                        <div className="flex h-10 w-10 items-center justify-center rounded-full bg-slate-950 text-sm font-semibold text-white">{driver.initials ?? driver.first_name.slice(0, 1)}</div>
                    )}
                    <div>
                        <p className="text-sm font-semibold text-slate-950">{driver.first_name}</p>
                        <p className="text-xs text-slate-500">Driver</p>
                    </div>
                </div>
            </div>
        </header>
    );
}

function StatCard({ card, value }: { card: { label: string; note: string; icon: string; suffix?: string }; value: string | number }) {
    const icon = card.icon === 'calendar' ? <IconCalendar /> : card.icon === 'check' ? <IconCheck /> : card.icon === 'bell' ? <IconBell /> : <IconCar />;

    return (
        <section className="rounded-2xl border border-slate-200 bg-white p-5">
            <div className="flex items-start justify-between gap-4">
                <div className="min-w-0">
                    <p className="truncate text-sm font-medium text-slate-500">{card.label}</p>
                    <p className="mt-2 text-3xl font-semibold tracking-tight text-slate-950">{value}{card.suffix}</p>
                    <p className="mt-1 truncate text-sm text-slate-500">{card.note}</p>
                </div>
                <span className="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-slate-200 bg-slate-50 text-slate-600">
                    {icon}
                </span>
            </div>
        </section>
    );
}

function RidesPanel({ rides, error }: { rides: Ride[]; error?: string }) {
    return (
        <section id="notifications" className="rounded-2xl border border-slate-200 bg-white p-6">
            <div className="flex items-center justify-between gap-4">
                <h2 className="text-lg font-semibold text-slate-950">My rides</h2>
                <Link href={path('rides.publish')} className="inline-flex shrink-0 items-center justify-center rounded-lg bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white">Publish ride</Link>
            </div>
            {error && <p className="mt-4 rounded-2xl bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700">{error}</p>}

            {rides.length > 0 ? (
                <div className="mt-5 space-y-3">
                    {rides.slice(0, 5).map((ride) => <DriverRideRow key={ride.id} ride={ride} />)}
                </div>
            ) : (
                <EmptyState title="No rides published" message="Your scheduled, completed, and cancelled rides will appear here." actionLabel="Publish ride" actionHref={path('rides.publish')} />
            )}
        </section>
    );
}

function DriverRideRow({ ride }: { ride: Ride }) {
    const form = useForm({});

    return (
        <article className="rounded-xl border border-slate-200 bg-slate-50 p-5">
            <div className="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div className="flex min-w-0 gap-4">
                    <span className="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600">
                        <IconArrow className="h-5 w-5" />
                    </span>
                    <div className="min-w-0">
                        <h3 className="break-words text-lg font-semibold leading-tight text-slate-950">{ride.departure_city?.name ?? 'Departure'} <span className="text-slate-300">→</span> {ride.arrival_city?.name ?? 'Arrival'}</h3>
                        <div className="mt-3 flex flex-wrap gap-2 text-sm font-medium text-slate-600">
                            <span className="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2"><IconCalendar className="h-4 w-4 text-brand-700" /> {ride.departure_datetime_label}</span>
                            <span className="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2"><IconCar className="h-4 w-4" /> {ride.available_seats_label}</span>
                            <span className="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2">{ride.price_label}</span>
                        </div>
                    </div>
                </div>
                <div className="flex shrink-0 flex-wrap items-center gap-2">
                    <StatusChip status={ride.status} />
                    {ride.can_edit && <Link href={path('rides.edit', ride.id)} className="rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-xs font-semibold text-slate-700 transition hover:border-brand-200 hover:text-brand-700">Edit</Link>}
                    {ride.can_cancel && <button type="button" onClick={() => form.patch(path('rides.cancel', ride.id))} disabled={form.processing} className="rounded-lg bg-rose-50 px-4 py-2.5 text-xs font-semibold text-rose-700 transition hover:bg-rose-100 disabled:opacity-50">Cancel</button>}
                    {ride.can_complete && <button type="button" onClick={() => form.patch(path('rides.complete', ride.id))} disabled={form.processing} className="rounded-lg bg-emerald-600 px-4 py-2.5 text-xs font-semibold text-white transition hover:bg-emerald-700 disabled:opacity-50">Complete</button>}
                </div>
            </div>
        </article>
    );
}

function NotificationsPanel({ notifications }: { notifications: Notification[] }) {
    return (
        <section className="rounded-2xl border border-slate-200 bg-white p-6">
            <div className="flex items-center justify-between">
                <h2 className="text-lg font-semibold text-slate-950">Notifications</h2>
                <span className="text-sm font-medium text-slate-500">Recent</span>
            </div>
            {notifications.length > 0 ? (
                <div className="mt-5 space-y-3">
                    {notifications.slice(0, 3).map((notification) => (
                        <article key={notification.id} className="rounded-xl border border-slate-200 bg-white p-4">
                            <div className="flex items-start gap-4">
                                <span className={`mt-6 h-2 w-2 shrink-0 rounded-full ${notification.is_read ? 'bg-slate-300' : 'bg-brand-600'}`} />
                                <span className="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-slate-50 text-slate-600"><IconCar /></span>
                                <div className="min-w-0 flex-1">
                                    <div className="flex flex-col gap-1 sm:flex-row sm:items-start sm:justify-between">
                                        <p className="font-semibold text-slate-950">{notification.title}</p>
                                        {notification.created_label && <p className="shrink-0 text-xs font-semibold text-slate-400">{notification.created_label}</p>}
                                    </div>
                                    <p className="mt-1 line-clamp-2 text-sm leading-6 text-slate-500">{notification.message}</p>
                                </div>
                            </div>
                        </article>
                    ))}
                </div>
            ) : (
                <EmptyState title="No notifications" message="Booking updates will appear here." compact />
            )}
        </section>
    );
}



function BookingRequests({ bookings, error }: { bookings: Booking[]; error?: string }) {
    return (
        <section id="booking-requests" className="rounded-2xl border border-slate-200 bg-white p-6">
            <div className="flex items-center justify-between gap-3">
                <h2 className="text-lg font-semibold text-slate-950">Recent booking requests</h2>
                <span className="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-500">{bookings.length} total</span>
            </div>
            {error && <p className="mt-4 rounded-2xl bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700">{error}</p>}

            {bookings.length > 0 ? (
                <>
                    <div className="mt-5 hidden overflow-x-auto rounded-xl border border-slate-200 lg:block">
                        <table className="min-w-full divide-y divide-slate-100 text-sm">
                            <thead className="bg-slate-50 text-left text-xs font-semibold text-slate-500">
                                <tr>
                                    <th className="px-4 py-3">Traveler</th>
                                    <th className="px-4 py-3">Route</th>
                                    <th className="px-4 py-3">Seats</th>
                                    <th className="px-4 py-3">Status</th>
                                    <th className="px-4 py-3">Action</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100 bg-white">
                                {bookings.slice(0, 6).map((booking) => <DriverBookingTableRow key={booking.id} booking={booking} />)}
                            </tbody>
                        </table>
                    </div>

                    <div className="mt-5 space-y-3 lg:hidden">
                        {bookings.slice(0, 6).map((booking) => <DriverBookingMobileCard key={booking.id} booking={booking} />)}
                    </div>
                </>
            ) : (
                <EmptyState title="No booking requests" message="New traveler requests will appear here as soon as seats are requested." />
            )}
        </section>
    );
}

function DriverBookingTableRow({ booking }: { booking: Booking }) {
    const ride = booking.ride;
    const traveler = booking.traveler;

    return (
        <tr>
            <td className="px-4 py-3">
                {traveler ? (
                    <Link href={path('profiles.travelers.show', traveler.id)} className="font-semibold text-slate-800 transition hover:text-brand-700">{traveler.name}</Link>
                ) : (
                    <p className="font-semibold text-slate-800">Traveler</p>
                )}
                <ContactLink contact={booking.traveler_contact} lockedLabel="Contact unlocks after acceptance" />
            </td>
            <td className="px-4 py-3 font-semibold text-slate-600">{ride?.departure_city?.name ?? 'Departure'} <span className="text-slate-400">-&gt;</span> {ride?.arrival_city?.name ?? 'Arrival'}</td>
            <td className="px-4 py-3 text-slate-600">{booking.seats_reserved}</td>
            <td className="px-4 py-3"><StatusChip status={booking.status} /></td>
            <td className="px-4 py-3"><BookingAction booking={booking} /></td>
        </tr>
    );
}

function DriverBookingMobileCard({ booking }: { booking: Booking }) {
    const ride = booking.ride;
    const traveler = booking.traveler;

    return (
        <article className="rounded-xl border border-slate-200 bg-white p-4">
            <div className="flex items-start justify-between gap-3">
                <div className="min-w-0">
                    <p className="break-words font-semibold text-slate-950">{ride?.departure_city?.name ?? 'Departure'} <span className="text-slate-400">-&gt;</span> {ride?.arrival_city?.name ?? 'Arrival'}</p>
                    <p className="mt-1 text-sm font-semibold text-slate-500">
                        {traveler ? (
                            <Link href={path('profiles.travelers.show', traveler.id)} className="transition hover:text-brand-700">{traveler.name}</Link>
                        ) : 'Traveler'} · {booking.seats_reserved} seat{booking.seats_reserved === 1 ? '' : 's'}
                    </p>
                    <ContactLink contact={booking.traveler_contact} lockedLabel="Contact unlocks after acceptance" />
                </div>
                <StatusChip status={booking.status} />
            </div>
            <div className="mt-4 flex flex-wrap gap-2">
                <BookingAction booking={booking} />
            </div>
        </article>
    );
}

function ContactLink({ contact, lockedLabel }: { contact?: BookingContact | null; lockedLabel: string }) {
    if (! contact) {
        return <p className="mt-1 text-xs font-medium text-slate-400">{lockedLabel}</p>;
    }

    return (
        <div className="mt-2 flex flex-wrap items-center gap-2">
            <span className="text-xs font-semibold text-slate-500">{contact.phone}</span>
            <a href={contact.whatsapp_url} target="_blank" rel="noreferrer" className="rounded-lg bg-emerald-50 px-3 py-1.5 text-xs font-black text-emerald-700 transition hover:bg-emerald-100">
                WhatsApp
            </a>
        </div>
    );
}

function BookingAction({ booking }: { booking: Booking }) {
    const confirm = useForm({});
    const reject = useForm({});

    if (booking.status !== 'pending') {
        return <span className="text-xs font-medium text-slate-400">No action</span>;
    }

    return (
        <div className="flex flex-wrap gap-2">
            <button type="button" onClick={() => confirm.patch(path('bookings.confirm', booking.id))} disabled={confirm.processing || reject.processing} className="rounded-lg bg-emerald-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-emerald-700 disabled:opacity-50">Accept</button>
            <button type="button" onClick={() => reject.patch(path('bookings.reject', booking.id))} disabled={confirm.processing || reject.processing} className="rounded-lg bg-rose-50 px-4 py-2 text-xs font-semibold text-rose-700 transition hover:bg-rose-100 disabled:opacity-50">Reject</button>
        </div>
    );
}

function EmptyState({ title, message, actionLabel, actionHref, compact = false }: { title: string; message: string; actionLabel?: string; actionHref?: string; compact?: boolean }) {
    return (
        <div className={`mt-5 rounded-[1rem] border border-dashed border-slate-200 bg-slate-50 text-center ${compact ? 'px-4 py-8' : 'px-6 py-12'}`}>
            <div className="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-50 text-brand-700">
                <IconCalendar />
            </div>
            <h3 className="mt-4 text-base font-semibold text-slate-950">{title}</h3>
            <p className="mx-auto mt-2 max-w-sm text-sm leading-6 text-slate-500">{message}</p>
            {actionHref && actionLabel && <Link href={actionHref} className="mt-5 inline-flex items-center justify-center rounded-lg bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white">{actionLabel}</Link>}
        </div>
    );
}
