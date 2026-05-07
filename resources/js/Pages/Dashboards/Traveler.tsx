import { Link, useForm, usePage } from '@inertiajs/react';
import { FormEvent, ReactNode, useState } from 'react';
import { Layout } from '../../components/Layout';
import { StatusChip } from '../../components/ui';
import { asset, path } from '../../routes';
import { Booking, Notification, SharedProps, UserSummary } from '../../types';

type TravelerProps = {
    traveler: UserSummary;
    bookings: Booking[];
    upcomingBookings: Booking[];
    notifications: Notification[];
    stats: Record<string, string | number>;
};

type IconProps = {
    className?: string;
};

const statCards = [
    { key: 'upcoming_trips', label: 'Upcoming trips', note: 'Trips coming up', icon: 'calendar' },
    { key: 'completed_trips', label: 'Completed trips', note: 'Finished rides', icon: 'check' },
    { key: 'cancelled_trips', label: 'Cancelled trips', note: 'No cancellations', icon: 'x' },
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

function IconX({ className = 'h-5 w-5' }: IconProps) {
    return <svg className={className} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.3" strokeLinecap="round"><circle cx="12" cy="12" r="9" /><path d="m15 9-6 6M9 9l6 6" /></svg>;
}

function IconStar({ className = 'h-5 w-5' }: IconProps) {
    return <svg className={className} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round"><polygon points="12 2 15.1 8.3 22 9.3 17 14.1 18.2 21 12 17.8 5.8 21 7 14.1 2 9.3 8.9 8.3 12 2" /></svg>;
}

function IconBell({ className = 'h-5 w-5' }: IconProps) {
    return <svg className={className} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9" /><path d="M13.7 21a2 2 0 0 1-3.4 0" /></svg>;
}

function IconArrow({ className = 'h-4 w-4' }: IconProps) {
    return <svg className={className} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round"><path d="M5 12h14M13 5l7 7-7 7" /></svg>;
}

function IconDots({ className = 'h-5 w-5' }: IconProps) {
    return <svg className={className} viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="5" r="1.8" /><circle cx="12" cy="12" r="1.8" /><circle cx="12" cy="19" r="1.8" /></svg>;
}

function IconPin({ className = 'h-4 w-4' }: IconProps) {
    return <svg className={className} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" /><circle cx="12" cy="10" r="3" /></svg>;
}

function IconClock({ className = 'h-4 w-4' }: IconProps) {
    return <svg className={className} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round"><circle cx="12" cy="12" r="9" /><path d="M12 7v5l3 2" /></svg>;
}

export default function Traveler({ traveler, bookings, upcomingBookings, notifications, stats }: TravelerProps) {
    const { errors } = usePage<SharedProps>().props;
    const nextTrip = upcomingBookings[0];
    const unreadNotifications = notifications.filter((notification) => ! notification.is_read).length;

    return (
        <Layout title="Traveler Dashboard" showHeader={false} showFooter={false}>
            <section className="min-h-screen bg-[#f7faff] text-slate-950">
                <div className="grid min-h-screen lg:grid-cols-[280px_minmax(0,1fr)]">
                    <TravelerSidebar />

                    <main className="min-w-0 px-4 py-5 sm:px-6 lg:px-8">
                        <TopBar traveler={traveler} unreadNotifications={unreadNotifications} />

                        <div className="mt-6 space-y-6">
                            <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                                {statCards.map((card) => <StatCard key={card.key} card={card} value={stats[card.key] ?? '0'} />)}
                            </div>

                            <div className="grid gap-6 xl:grid-cols-[minmax(0,1fr)_420px]">
                                <div className="min-w-0 space-y-6">
                                    <NextTripPanel booking={nextTrip} />
                                </div>
                                <aside className="min-w-0 space-y-5">
                                    <NotificationsPanel notifications={notifications} />
                                </aside>
                            </div>

                            <BookingHistory bookings={bookings} errors={errors} />
                        </div>
                    </main>
                </div>
            </section>
        </Layout>
    );
}

function TravelerSidebar() {
    return (
        <aside className="hidden border-r border-slate-200/80 bg-white/90 px-4 py-7 shadow-[18px_0_55px_-44px_rgba(15,23,42,0.35)] backdrop-blur-xl lg:block">
            <div className="sticky top-7 flex h-[calc(100vh-3.5rem)] flex-col">
                <Link href={path('home')} className="flex items-center gap-3 px-3">
                    <img src={asset('assets/logoBlasaCar.png')} alt="BlasaCar" className="h-10 w-auto" />
                    <span className="text-2xl font-black tracking-tight">Blasa<span className="text-brand-600">Car</span></span>
                </Link>

                <nav className="mt-12 space-y-3">
                    <SideLink href={path('dashboards.traveler')} active icon={<IconHome />}>Overview</SideLink>
                    <SideLink href={path('rides.search')} icon={<IconSearch />}>Search rides</SideLink>
                    <SideLink href={path('drivers.onboarding.create')} icon={<IconCar />}>Become a driver</SideLink>
                    <SideLink href="#booking-history" icon={<IconCalendar />}>Booking history</SideLink>
                </nav>

                <div className="mt-auto rounded-[1.5rem] border border-brand-100 bg-gradient-to-b from-brand-50 to-white p-4 text-center shadow-sm">
                    <div className="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-brand-700 shadow-sm">
                        <IconCar className="h-7 w-7" />
                    </div>
                    <h3 className="mt-4 text-base font-black text-slate-950">Drive. Earn. Explore.</h3>
                    <p className="mt-2 text-xs leading-5 text-slate-500">Join our community of trusted drivers.</p>
                    <Link href={path('drivers.onboarding.create')} className="mt-5 inline-flex w-full items-center justify-center rounded-2xl bg-brand-600 px-4 py-3 text-sm font-bold text-white transition hover:bg-brand-700">Become a driver</Link>
                </div>
            </div>
        </aside>
    );
}

function SideLink({ href, active = false, icon, children }: { href: string; active?: boolean; icon: ReactNode; children: ReactNode }) {
    return (
        <Link href={href} className={`flex items-center gap-4 rounded-[1.15rem] px-5 py-4 text-sm font-black transition ${active ? 'bg-brand-50 text-brand-700' : 'text-slate-600 hover:bg-slate-50 hover:text-brand-700'}`}>
            {icon}
            {children}
        </Link>
    );
}

function TopBar({ traveler, unreadNotifications }: { traveler: UserSummary; unreadNotifications: number }) {
    return (
        <header className="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div className="flex items-center gap-3 lg:hidden">
                <img src={asset('assets/logoBlasaCar.png')} alt="BlasaCar" className="h-9 w-auto" />
                <span className="text-xl font-black">Blasa<span className="text-brand-600">Car</span></span>
            </div>

            <Link href={path('rides.search')} className="flex min-h-12 w-full max-w-xl items-center gap-3 rounded-[1.15rem] border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-500 shadow-sm transition hover:border-brand-200 hover:text-brand-700">
                <IconSearch className="h-5 w-5 shrink-0" />
                <span className="min-w-0 flex-1 truncate">Search for destinations, routes...</span>
                <span className="hidden rounded-lg bg-slate-100 px-2 py-1 text-xs font-black text-slate-400 sm:inline">K</span>
            </Link>

            <div className="flex items-center justify-between gap-4 md:justify-end">
                <div className="relative flex h-11 w-11 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-600 shadow-sm">
                    <IconBell />
                    {unreadNotifications > 0 && <span className="absolute -right-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-brand-600 px-1 text-[10px] font-black text-white">{unreadNotifications}</span>}
                </div>
                <div className="flex items-center gap-3">
                    <div className="flex h-12 w-12 items-center justify-center rounded-full border-2 border-brand-300 bg-brand-50 text-sm font-black text-brand-700">{traveler.initials ?? traveler.first_name.slice(0, 1)}</div>
                    <div>
                        <p className="font-black text-slate-950">{traveler.first_name}</p>
                        <p className="text-sm font-medium text-slate-500">Traveler</p>
                    </div>
                </div>
            </div>
        </header>
    );
}

function StatCard({ card, value }: { card: { label: string; note: string; icon: string }; value: string | number }) {
    const icon = card.icon === 'calendar' ? <IconCalendar /> : card.icon === 'check' ? <IconCheck /> : card.icon === 'x' ? <IconX /> : <IconStar />;

    return (
        <section className="rounded-[1.25rem] border border-slate-200 bg-white p-5 shadow-[0_18px_55px_-45px_rgba(15,23,42,0.45)]">
            <div className="flex items-center gap-4">
                <span className="flex h-14 w-14 shrink-0 items-center justify-center rounded-full border border-brand-100 bg-brand-50 text-brand-700">
                    {icon}
                </span>
                <div className="min-w-0 flex-1">
                    <div className="flex items-start justify-between gap-3">
                        <div className="min-w-0">
                            <p className="truncate text-sm font-bold text-slate-600">{card.label}</p>
                            <p className="mt-2 text-3xl font-black leading-none text-slate-950">{value}</p>
                        </div>
                        <IconArrow className="mt-1 h-4 w-4 shrink-0 text-slate-400" />
                    </div>
                    <p className="mt-2 truncate text-xs font-medium text-slate-500">{card.note}</p>
                </div>
            </div>
        </section>
    );
}

function NextTripPanel({ booking }: { booking?: Booking }) {
    if (! booking) {
        return (
            <section className="rounded-[1.25rem] border border-slate-200 bg-white p-6 shadow-sm">
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 className="text-xl font-black text-slate-950">Next confirmed trip</h2>
                        <p className="mt-2 text-sm text-slate-500">No upcoming trips are scheduled for this account yet.</p>
                    </div>
                    <Link href={path('rides.search')} className="brand-button">Find a ride</Link>
                </div>
            </section>
        );
    }

    const ride = booking.ride;

    return (
        <section className="rounded-[1.25rem] border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <h2 className="text-xl font-black text-slate-950">Next confirmed trip</h2>
            <div className="mt-5 rounded-[1.15rem] border border-slate-200 bg-gradient-to-br from-white to-brand-50/50 p-5">
                <div className="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                    <div className="flex min-w-0 gap-4">
                        <span className="flex h-16 w-16 shrink-0 items-center justify-center rounded-full bg-brand-50 text-brand-700">
                            <IconArrow className="h-7 w-7" />
                        </span>
                        <div className="min-w-0">
                            <div className="flex flex-wrap items-center gap-3">
                                <h3 className="break-words text-2xl font-black text-slate-950">{ride?.departure_city?.name ?? 'Departure'} <span className="text-slate-400">-&gt;</span> {ride?.arrival_city?.name ?? 'Arrival'}</h3>
                                <StatusChip status={booking.status} />
                            </div>
                            <div className="mt-4 flex flex-wrap gap-x-5 gap-y-2 text-sm font-semibold text-slate-600">
                                <span className="inline-flex items-center gap-2"><IconCalendar className="h-4 w-4" /> {ride?.departure_date ?? 'Date not set'}</span>
                                <span className="inline-flex items-center gap-2"><IconClock /> {ride?.departure_time_label ?? ride?.departure_datetime_label ?? 'Time not set'}</span>
                                <span className="inline-flex items-center gap-2"><IconPin /> {ride?.meeting_point ?? 'Meeting point not set'}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="mt-5 grid gap-4 border-t border-slate-200 pt-5 sm:grid-cols-3">
                    <TripInfo label="Driver" value={ride?.driver?.name ?? 'Not listed'} sub={ride?.driver?.profile?.avg_rating ? `${ride.driver.profile.avg_rating} rating` : undefined} />
                    <TripInfo label="Seats" value={String(booking.seats_reserved)} />
                    <TripInfo label="Price" value={ride?.price_label ?? '0 DH'} />
                </div>

                <div className="mt-5 flex justify-end">
                    {ride && <Link href={path('rides.show', ride.id)} className="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-black text-brand-700 transition hover:border-brand-200 hover:bg-brand-50">View trip details <IconArrow /></Link>}
                </div>
            </div>
        </section>
    );
}

function TripInfo({ label, value, sub }: { label: string; value: string; sub?: string }) {
    return (
        <div className="min-w-0 border-slate-200 sm:border-r last:border-r-0">
            <p className="text-xs font-bold text-slate-500">{label}</p>
            <p className="mt-1 truncate text-sm font-black text-slate-900">{value}</p>
            {sub && <p className="mt-1 text-xs font-semibold text-brand-700">{sub}</p>}
        </div>
    );
}

function NotificationsPanel({ notifications }: { notifications: Notification[] }) {
    return (
        <section className="rounded-[1.25rem] border border-slate-200 bg-white p-5 shadow-sm">
            <div className="flex items-center justify-between">
                <h2 className="text-xl font-black text-slate-950">Notifications</h2>
                <span className="text-sm font-black text-brand-700">View all</span>
            </div>
            {notifications.length > 0 ? (
                <div className="mt-5 space-y-3">
                    {notifications.slice(0, 3).map((notification) => (
                        <article key={notification.id} className="rounded-[1rem] border border-slate-200 bg-white p-4">
                            <div className="flex items-start gap-4">
                                <span className={`mt-6 h-2 w-2 shrink-0 rounded-full ${notification.is_read ? 'bg-slate-300' : 'bg-brand-600'}`} />
                                <span className="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-brand-50 text-brand-700"><IconCar /></span>
                                <div className="min-w-0 flex-1">
                                    <div className="flex flex-col gap-1 sm:flex-row sm:items-start sm:justify-between">
                                        <p className="font-black text-slate-950">{notification.title}</p>
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

function BookingHistory({ bookings, errors }: { bookings: Booking[]; errors: Record<string, string> }) {
    return (
        <section id="booking-history" className="rounded-[1.25rem] border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <div className="flex items-center justify-between gap-3">
                <h2 className="text-xl font-black text-slate-950">Booking history</h2>
                <span className="rounded-full bg-slate-100 px-3 py-1 text-xs font-black text-slate-500">{bookings.length} total</span>
            </div>

            {['booking', 'review', 'rating', 'comment'].map((key) => errors[key] && <p key={key} className="mt-4 rounded-2xl bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700">{errors[key]}</p>)}

            {bookings.length > 0 ? (
                <>
                    <div className="mt-5 hidden overflow-x-auto rounded-[1rem] border border-slate-200 lg:block">
                        <table className="min-w-full divide-y divide-slate-200 text-sm">
                            <thead className="bg-slate-50 text-left text-xs font-black text-slate-500">
                                <tr>
                                    <th className="px-4 py-3">Route</th>
                                    <th className="px-4 py-3">Date</th>
                                    <th className="px-4 py-3">Driver</th>
                                    <th className="px-4 py-3">Status</th>
                                    <th className="px-4 py-3">Price</th>
                                    <th className="px-4 py-3">Action</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100 bg-white">
                                {bookings.map((booking) => <BookingTableRow key={booking.id} booking={booking} />)}
                            </tbody>
                        </table>
                    </div>

                    <div className="mt-5 space-y-3 lg:hidden">
                        {bookings.map((booking) => <BookingMobileCard key={booking.id} booking={booking} />)}
                    </div>
                </>
            ) : (
                <EmptyState title="No bookings yet" message="Requested, accepted, completed, and cancelled trips will appear here." actionLabel="Find a ride" actionHref={path('rides.search')} />
            )}
        </section>
    );
}

function BookingTableRow({ booking }: { booking: Booking }) {
    const [showReview, setShowReview] = useState(false);
    const ride = booking.ride;

    return (
        <>
            <tr>
                <td className="px-4 py-3 font-black text-slate-800">{ride?.departure_city?.name ?? 'Departure'} <span className="text-slate-400">-&gt;</span> {ride?.arrival_city?.name ?? 'Arrival'}</td>
                <td className="px-4 py-3 font-semibold text-slate-600">{ride?.departure_datetime_label ?? 'Not set'}</td>
                <td className="px-4 py-3 font-semibold text-slate-700">{ride?.driver?.name ?? 'Not listed'}</td>
                <td className="px-4 py-3"><StatusChip status={booking.status} /></td>
                <td className="px-4 py-3 font-black text-slate-700">{ride?.price_label ?? '0 DH'}</td>
                <td className="px-4 py-3">
                    <div className="flex items-center gap-2">
                        {ride && <Link href={path('rides.show', ride.id)} className="rounded-xl border border-slate-200 px-3 py-1.5 text-xs font-black text-slate-600 transition hover:border-brand-200 hover:text-brand-700">View</Link>}
                        <BookingAction booking={booking} reviewOpen={showReview} onToggleReview={() => setShowReview(! showReview)} />
                        <IconDots className="h-5 w-5 text-slate-400" />
                    </div>
                </td>
            </tr>
            {showReview && booking.can_review && (
                <tr>
                    <td colSpan={6} className="bg-slate-50 px-4 py-4">
                        <ReviewForm booking={booking} onSuccess={() => setShowReview(false)} />
                    </td>
                </tr>
            )}
        </>
    );
}

function BookingMobileCard({ booking }: { booking: Booking }) {
    const [showReview, setShowReview] = useState(false);
    const ride = booking.ride;

    return (
        <article className="rounded-[1rem] border border-slate-200 bg-slate-50 p-4">
            <div className="flex items-start justify-between gap-3">
                <div className="min-w-0">
                    <p className="break-words font-black text-slate-950">{ride?.departure_city?.name ?? 'Departure'} <span className="text-slate-400">-&gt;</span> {ride?.arrival_city?.name ?? 'Arrival'}</p>
                    <p className="mt-1 text-sm font-semibold text-slate-500">{ride?.departure_datetime_label ?? 'Not set'}</p>
                </div>
                <StatusChip status={booking.status} />
            </div>
            <div className="mt-4 grid grid-cols-2 gap-3 text-sm">
                <TripInfo label="Driver" value={ride?.driver?.name ?? 'Not listed'} />
                <TripInfo label="Price" value={ride?.price_label ?? '0 DH'} />
            </div>
            <div className="mt-4 flex flex-wrap gap-2">
                {ride && <Link href={path('rides.show', ride.id)} className="rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-black text-slate-600">View</Link>}
                <BookingAction booking={booking} reviewOpen={showReview} onToggleReview={() => setShowReview(! showReview)} />
            </div>
            {showReview && booking.can_review && <ReviewForm booking={booking} onSuccess={() => setShowReview(false)} />}
        </article>
    );
}

function BookingAction({ booking, reviewOpen, onToggleReview }: { booking: Booking; reviewOpen: boolean; onToggleReview: () => void }) {
    const cancel = useForm({});

    if (booking.can_cancel) {
        return <button type="button" onClick={() => cancel.patch(path('bookings.cancel', booking.id))} disabled={cancel.processing} className="rounded-xl bg-rose-50 px-3 py-2 text-xs font-black text-rose-700 transition hover:bg-rose-100 disabled:opacity-50">Cancel</button>;
    }

    if (booking.can_review) {
        return <button type="button" onClick={onToggleReview} className="rounded-xl bg-brand-50 px-3 py-2 text-xs font-black text-brand-700 transition hover:bg-brand-100">{reviewOpen ? 'Close' : 'Review'}</button>;
    }

    if (booking.reviewed) {
        return <span className="inline-flex items-center gap-1.5 rounded-xl bg-emerald-50 px-3 py-2 text-xs font-black text-emerald-700"><IconCheck className="h-4 w-4" /> Reviewed</span>;
    }

    return null;
}

function ReviewForm({ booking, onSuccess }: { booking: Booking; onSuccess: () => void }) {
    const review = useForm({ rating: '', comment: '' });

    const submitReview = (event: FormEvent) => {
        event.preventDefault();
        review.post(path('bookings.reviews.store', booking.id), { onSuccess });
    };

    return (
        <form onSubmit={submitReview} className="mt-4 grid gap-3 rounded-[1rem] border border-slate-200 bg-white p-4 lg:mt-0 lg:grid-cols-[180px_minmax(0,1fr)_120px] lg:items-end">
            <div>
                <label className="text-xs font-bold uppercase tracking-[0.14em] text-slate-400">Rating</label>
                <StarRating value={review.data.rating} onChange={(value) => review.setData('rating', value)} />
            </div>
            <div>
                <label className="text-xs font-bold uppercase tracking-[0.14em] text-slate-400">Comment</label>
                <textarea
                    value={review.data.comment}
                    onChange={(event) => review.setData('comment', event.target.value)}
                    rows={2}
                    maxLength={1000}
                    placeholder="Share your experience"
                    className="mt-2 w-full resize-none rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-brand-400 focus:bg-white focus:ring-4 focus:ring-brand-100"
                />
            </div>
            <button type="submit" disabled={! review.data.rating || review.processing} className="brand-button rounded-xl px-5 py-3 text-xs disabled:cursor-not-allowed disabled:opacity-40">Submit</button>
        </form>
    );
}

function StarRating({ value, onChange }: { value: string; onChange: (value: string) => void }) {
    const [hover, setHover] = useState(0);
    const selected = Number(value) || 0;

    return (
        <div className="mt-2 flex gap-1">
            {[1, 2, 3, 4, 5].map((star) => (
                <button
                    key={star}
                    type="button"
                    onMouseEnter={() => setHover(star)}
                    onMouseLeave={() => setHover(0)}
                    onClick={() => onChange(String(star))}
                    className={`rounded-full p-1 transition ${star <= (hover || selected) ? 'text-amber-500' : 'text-slate-300 hover:text-amber-300'}`}
                    aria-label={`Rate ${star}`}
                >
                    <IconStar className="h-6 w-6" />
                </button>
            ))}
        </div>
    );
}

function EmptyState({ title, message, actionLabel, actionHref, compact = false }: { title: string; message: string; actionLabel?: string; actionHref?: string; compact?: boolean }) {
    return (
        <div className={`mt-5 rounded-[1rem] border border-dashed border-slate-200 bg-slate-50 text-center ${compact ? 'px-4 py-8' : 'px-6 py-12'}`}>
            <div className="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-50 text-brand-700">
                <IconCalendar />
            </div>
            <h3 className="mt-4 text-base font-black text-slate-950">{title}</h3>
            <p className="mx-auto mt-2 max-w-sm text-sm leading-6 text-slate-500">{message}</p>
            {actionHref && actionLabel && <Link href={actionHref} className="brand-button mt-5">{actionLabel}</Link>}
        </div>
    );
}
