import { Link, usePage } from '@inertiajs/react';
import { AuthPrompt, IconArrowRight, IconCar, IconTicket, IconUser, LogoutButton, MobileShell } from '../../components/MobileShell';
import { path } from '../../routes';
import { Booking, Notification, SharedProps } from '../../types';

type AccountProps = {
    bookings: Booking[];
    notifications: Notification[];
    stats: {
        upcoming_trips: number;
        completed_trips: number;
        cancelled_trips: number;
    };
};

// Shows mobile account details, stats, quick links, and notifications.
export default function Account({ bookings, notifications, stats }: AccountProps) {
    const { auth } = usePage<SharedProps>().props;

    if (! auth.user) {
        return (
            <MobileShell title="Account" active="account">
                <AuthPrompt title="Your account is waiting" message="Log in to manage bookings, driver setup, notifications, and profile settings." />
            </MobileShell>
        );
    }

    return (
        <MobileShell title="Account" active="account">
            <div className="space-y-5 px-5 pb-6 pt-5">
                <section className="rounded-lg bg-slate-950 p-5 text-white shadow-xl shadow-slate-900/10">
                    <div className="flex items-center gap-4">
                        <div className="flex h-16 w-16 shrink-0 items-center justify-center rounded-full bg-white text-xl font-black text-slate-950">
                            {auth.user.initials ?? auth.user.first_name.slice(0, 1).toUpperCase()}
                        </div>
                        <div className="min-w-0">
                            <h1 className="truncate text-2xl font-black">{auth.user.name}</h1>
                            <p className="mt-1 truncate text-sm font-semibold text-white/65">{auth.user.email}</p>
                            <p className="mt-2 inline-flex rounded-full bg-white/10 px-3 py-1 text-xs font-black capitalize text-brand-100">{auth.user.role}</p>
                        </div>
                    </div>
                </section>

                <section className="grid grid-cols-3 gap-2">
                    <Stat value={stats.upcoming_trips} label="Upcoming" />
                    <Stat value={stats.completed_trips} label="Done" />
                    <Stat value={stats.cancelled_trips} label="Canceled" />
                </section>

                <section className="rounded-lg border border-slate-200 bg-white p-2 shadow-sm">
                    <AccountLink href={path('mobile.trips')} icon={<IconTicket />} label="My trips" />
                    <AccountLink href={path(auth.user.dashboard_route)} icon={<IconUser />} label="Dashboard" />
                    <AccountLink href={path('account.settings.edit')} icon={<IconUser />} label="Profile settings" />
                    <AccountLink href={path('rides.publish')} icon={<IconCar />} label="Publish a ride" />
                    {auth.user.role === 'traveler' && (
                        <AccountLink href={path('drivers.onboarding.create')} icon={<IconCar />} label="Become a driver" />
                    )}
                </section>

                <section>
                    <div className="mb-3 flex items-center justify-between">
                        <h2 className="text-lg font-black text-slate-950">Notifications</h2>
                        <p className="text-xs font-bold text-slate-500">{notifications.length} recent</p>
                    </div>
                    <div className="space-y-2">
                        {notifications.length > 0 ? (
                            notifications.map((notification) => (
                                <div key={notification.id} className="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                                    <div className="flex items-start gap-3">
                                        <span className={`mt-1.5 h-2.5 w-2.5 shrink-0 rounded-full ${notification.is_read ? 'bg-slate-300' : 'bg-brand-500'}`} />
                                        <div className="min-w-0">
                                            <p className="text-sm font-black text-slate-950">{notification.title}</p>
                                            <p className="mt-1 text-xs font-semibold leading-5 text-slate-500">{notification.message}</p>
                                            {notification.created_label && <p className="mt-2 text-[11px] font-bold text-slate-400">{notification.created_label}</p>}
                                        </div>
                                    </div>
                                </div>
                            ))
                        ) : (
                            <p className="rounded-lg border border-slate-200 bg-white p-4 text-sm font-semibold text-slate-500 shadow-sm">No notifications yet.</p>
                        )}
                    </div>
                </section>

                {bookings.length > 0 && (
                    <section className="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                        <p className="text-sm font-black text-slate-950">Recent booking</p>
                        <p className="mt-1 text-sm font-semibold text-slate-500">
                            {bookings[0].ride?.departure_city?.name ?? 'Departure'} to {bookings[0].ride?.arrival_city?.name ?? 'Arrival'}
                        </p>
                    </section>
                )}

                <LogoutButton />
            </div>
        </MobileShell>
    );
}

// Renders one mobile account statistic.
function Stat({ value, label }: { value: number; label: string }) {
    return (
        <div className="rounded-lg border border-slate-200 bg-white px-3 py-4 text-center shadow-sm">
            <p className="text-2xl font-black text-slate-950">{value}</p>
            <p className="mt-1 text-[11px] font-black text-slate-500">{label}</p>
        </div>
    );
}

// Renders one mobile account navigation link.
function AccountLink({ href, icon, label }: { href: string; icon: React.ReactNode; label: string }) {
    return (
        <Link href={href} className="flex min-h-12 items-center justify-between rounded-lg px-3 py-2 text-sm font-black text-slate-800 active:bg-slate-50">
            <span className="flex items-center gap-3">
                <span className="flex h-9 w-9 items-center justify-center rounded-full bg-slate-100 text-slate-700">{icon}</span>
                {label}
            </span>
            <IconArrowRight />
        </Link>
    );
}
