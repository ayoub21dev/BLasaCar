import { Link, usePage } from '@inertiajs/react';
import { IconArrowRight, IconCar, IconSearch, IconTicket, IconUser, MobileShell } from '../../components/MobileShell';
import { MobileRideCard } from '../../components/MobileRideCard';
import { MobileSearchForm } from '../../components/MobileSearchForm';
import { path } from '../../routes';
import { City, Ride, SharedProps } from '../../types';

type HomeProps = {
    cities: City[];
    featuredRides: Ride[];
    today: string;
};

// Shows the mobile home screen with search, quick actions, and featured rides.
export default function Home({ cities, featuredRides, today }: HomeProps) {
    const { auth } = usePage<SharedProps>().props;
    const firstName = auth.user?.first_name ?? 'Traveler';

    return (
        <MobileShell title="Home" active="home">
            <div className="space-y-6 px-5 pb-6 pt-5">
                <section className="rounded-lg bg-slate-950 p-5 text-white shadow-xl shadow-slate-900/10">
                    <p className="text-xs font-black uppercase tracking-wide text-brand-200">Good to see you</p>
                    <h1 className="mt-2 text-3xl font-black tracking-tight">{firstName}, where to?</h1>
                    <p className="mt-2 text-sm font-semibold leading-6 text-white/70">
                        Find a ride, check bookings, and keep your BlasaCar account close.
                    </p>
                    <div className="mt-5 grid grid-cols-3 gap-2 text-center">
                        <Metric value={featuredRides.length} label="Rides" />
                        <Metric value={cities.length} label="Cities" />
                        <Metric value="4" label="Seats max" />
                    </div>
                </section>

                <section>
                    <div className="mb-3 flex items-center justify-between">
                        <h2 className="text-lg font-black text-slate-950">Find a ride</h2>
                        <Link href={path('mobile.search')} className="inline-flex items-center gap-1 text-xs font-black text-brand-700">
                            Advanced
                            <IconArrowRight />
                        </Link>
                    </div>
                    <MobileSearchForm cities={cities} defaultDate={today} buttonLabel="Search rides" />
                </section>

                <section>
                    <h2 className="mb-3 text-lg font-black text-slate-950">Quick actions</h2>
                    <div className="grid grid-cols-4 gap-2">
                        <QuickAction href={path('mobile.search')} icon={<IconSearch />} label="Search" />
                        <QuickAction href={path('mobile.trips')} icon={<IconTicket />} label="Trips" />
                        <QuickAction href={path('mobile.account')} icon={<IconUser />} label="Account" />
                        <QuickAction href={path('rides.publish')} icon={<IconCar />} label="Drive" />
                    </div>
                </section>

                <section>
                    <div className="mb-3 flex items-center justify-between">
                        <h2 className="text-lg font-black text-slate-950">Leaving soon</h2>
                        <Link href={path('mobile.search')} className="text-xs font-black text-brand-700">View all</Link>
                    </div>
                    <div className="space-y-3">
                        {featuredRides.length > 0 ? (
                            featuredRides.map((ride) => <MobileRideCard key={ride.id} ride={ride} ctaLabel="Book" />)
                        ) : (
                            <EmptyPanel title="No rides yet" message="Seed rides are not loaded for this database." />
                        )}
                    </div>
                </section>
            </div>
        </MobileShell>
    );
}

// Renders one small metric in the mobile home hero.
function Metric({ value, label }: { value: string | number; label: string }) {
    return (
        <div className="rounded-lg bg-white/10 px-2 py-3">
            <p className="text-xl font-black">{value}</p>
            <p className="text-[11px] font-bold text-white/60">{label}</p>
        </div>
    );
}

// Renders one shortcut tile on the mobile home screen.
function QuickAction({ href, icon, label }: { href: string; icon: React.ReactNode; label: string }) {
    return (
        <Link href={href} className="rounded-lg border border-slate-200 bg-white px-2 py-3 text-center shadow-sm">
            <span className="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-slate-950 text-white">{icon}</span>
            <span className="mt-2 block text-[11px] font-black text-slate-700">{label}</span>
        </Link>
    );
}

// Shows an empty state when no featured ride data is available.
function EmptyPanel({ title, message }: { title: string; message: string }) {
    return (
        <div className="rounded-lg border border-dashed border-slate-300 bg-white p-5 text-center">
            <p className="font-black text-slate-800">{title}</p>
            <p className="mt-1 text-sm font-semibold leading-6 text-slate-500">{message}</p>
        </div>
    );
}
