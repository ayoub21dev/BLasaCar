import { Link, useForm, usePage } from '@inertiajs/react';
import { ReactNode } from 'react';
import { Layout } from '../../components/Layout';
import { StatusChip } from '../../components/ui';
import { asset, path } from '../../routes';
import { DriverProfile, Ride, SharedProps, UserSummary } from '../../types';

type AdminSection = 'overview' | 'driver-verification' | 'users' | 'rides';

type AdminProps = {
    section: AdminSection;
    metrics: Record<string, number>;
    alerts: Record<string, number>;
    users: UserSummary[];
    rides: Ride[];
    pendingDriverProfiles: Array<DriverProfile & { user: UserSummary }>;
};

type IconProps = {
    className?: string;
};

const statCards = [
    { key: 'total_users', label: 'Total users', note: 'All accounts', icon: 'users' },
    { key: 'pending_driver_verifications', label: 'Pending ID checks', note: 'Need review', icon: 'shield' },
    { key: 'verified_drivers', label: 'Verified drivers', note: 'Approved profiles', icon: 'check' },
    { key: 'total_bookings', label: 'Bookings', note: 'All requests', icon: 'calendar' },
];

function IconHome({ className = 'h-5 w-5' }: IconProps) {
    return <svg className={className} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round"><path d="m3 11 9-8 9 8" /><path d="M5 10v10h14V10" /><path d="M9 20v-6h6v6" /></svg>;
}

function IconShield({ className = 'h-5 w-5' }: IconProps) {
    return <svg className={className} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round"><path d="M12 3 4 7v5c0 5 3.4 8.3 8 9 4.6-.7 8-4 8-9V7l-8-4Z" /><path d="m9 12 2 2 4-4" /></svg>;
}

function IconUsers({ className = 'h-5 w-5' }: IconProps) {
    return <svg className={className} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" /><circle cx="9" cy="7" r="4" /><path d="M22 21v-2a4 4 0 0 0-3-3.87" /><path d="M16 3.13a4 4 0 0 1 0 7.75" /></svg>;
}

function IconCar({ className = 'h-5 w-5' }: IconProps) {
    return <svg className={className} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round"><path d="M19 17h2l-1.4-4.2A3 3 0 0 0 16.8 11H7.2a3 3 0 0 0-2.8 1.8L3 17h2" /><circle cx="7" cy="17" r="2" /><circle cx="17" cy="17" r="2" /><path d="M8 11l1.4-4h5.2L16 11" /></svg>;
}

function IconSearch({ className = 'h-5 w-5' }: IconProps) {
    return <svg className={className} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round"><circle cx="11" cy="11" r="8" /><path d="m21 21-4.3-4.3" /></svg>;
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

export default function Admin(props: AdminProps) {
    const { auth, errors } = usePage<SharedProps>().props;
    const admin = auth.user;
    const pendingCount = props.pendingDriverProfiles.length;

    return (
        <Layout title="Admin Dashboard" showHeader={false} showFooter={false}>
            <section className="min-h-screen bg-slate-50 text-slate-950">
                <div className="grid min-h-screen lg:grid-cols-[280px_minmax(0,1fr)]">
                    <AdminSidebar section={props.section} />

                    <main className="min-w-0 px-4 py-5 sm:px-6 lg:px-8">
                        <TopBar admin={admin} pendingCount={pendingCount} />

                        <div className="mx-auto mt-6 max-w-[1320px] space-y-6">
                            {errors.driver_profile && <div className="rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-semibold text-rose-700">{errors.driver_profile}</div>}
                            {errors.user && <div className="rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-semibold text-rose-700">{errors.user}</div>}
                            {errors.ride && <div className="rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-semibold text-rose-700">{errors.ride}</div>}
                            <PageHeader section={props.section} admin={admin} pendingCount={pendingCount} metrics={props.metrics} alerts={props.alerts} />

                            {props.section === 'overview' && <Overview {...props} />}
                            {props.section === 'driver-verification' && <DriverVerification {...props} />}
                            {props.section === 'users' && <Users {...props} />}
                            {props.section === 'rides' && <Rides {...props} />}
                        </div>
                    </main>
                </div>
            </section>
        </Layout>
    );
}

function AdminSidebar({ section }: { section: AdminSection }) {
    return (
        <aside className="hidden border-r border-slate-200 bg-white px-4 py-6 lg:block">
            <div className="sticky top-6 flex h-[calc(100vh-3rem)] flex-col">
                <Link href={path('home')} className="flex items-center gap-3 px-3 py-2">
                    <img src={asset('assets/logoBlasaCar.png')} alt="BlasaCar" className="h-9 w-auto" />
                    <span className="text-xl font-semibold tracking-tight text-slate-950">Blasa<span className="text-brand-600">Car</span></span>
                </Link>

                <div className="mt-8 px-3">
                    <p className="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Admin</p>
                </div>
                <nav className="mt-3 space-y-1">
                    <SideLink href={path('dashboards.admin')} active={section === 'overview'} icon={<IconHome />}>Overview</SideLink>
                    <SideLink href={path('dashboards.admin.driver-verification')} active={section === 'driver-verification'} icon={<IconShield />}>Driver verification</SideLink>
                    <SideLink href={path('dashboards.admin.users')} active={section === 'users'} icon={<IconUsers />}>All users</SideLink>
                    <SideLink href={path('dashboards.admin.rides')} active={section === 'rides'} icon={<IconCar />}>Ride activity</SideLink>
                    <SideLink href={path('rides.search')} icon={<IconSearch />}>Search rides</SideLink>
                </nav>

                <div className="mt-auto rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <h3 className="text-sm font-semibold text-slate-950">Driver reviews</h3>
                    <p className="mt-2 text-sm leading-6 text-slate-500">Check CIN photos before approving driver profiles.</p>
                    <Link href={path('dashboards.admin.driver-verification')} className="mt-4 inline-flex w-full items-center justify-center rounded-lg bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white">Review drivers</Link>
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

function TopBar({ admin, pendingCount }: { admin: UserSummary | null; pendingCount: number }) {
    const initials = admin?.initials ?? admin?.first_name.slice(0, 1) ?? 'A';

    return (
        <header className="mx-auto flex max-w-[1320px] flex-col gap-3 rounded-2xl border border-slate-200 bg-white p-3 md:flex-row md:items-center md:justify-between">
            <div className="flex items-center gap-3 px-2 lg:hidden">
                <img src={asset('assets/logoBlasaCar.png')} alt="BlasaCar" className="h-9 w-auto" />
                <span className="text-xl font-semibold tracking-tight">Blasa<span className="text-brand-600">Car</span></span>
            </div>

            <div className="flex items-center justify-between gap-4 px-1 md:ml-auto md:justify-end">
                <div className="relative flex h-11 w-11 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-600">
                    <IconBell />
                    {pendingCount > 0 && <span className="absolute -right-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-brand-600 px-1 text-[10px] font-black text-white">{pendingCount}</span>}
                </div>
                <div className="flex items-center gap-3">
                    <div className="flex h-10 w-10 items-center justify-center rounded-full bg-slate-950 text-sm font-semibold text-white">{initials}</div>
                    <div>
                        <p className="text-sm font-semibold text-slate-950">{admin?.first_name ?? 'Admin'}</p>
                        <p className="text-xs text-slate-500">Admin</p>
                    </div>
                </div>
            </div>
        </header>
    );
}

function PageHeader({ section, admin, pendingCount, metrics, alerts }: { section: AdminSection; admin: UserSummary | null; pendingCount: number; metrics: Record<string, number>; alerts: Record<string, number> }) {
    const header = {
        overview: {
            eyebrow: 'Admin dashboard',
            title: `Welcome back, ${admin?.first_name ?? 'Admin'}`,
            copy: 'Review driver identities, inspect users, and monitor ride activity from one clean place.',
            label: 'Pending reviews',
            value: `${pendingCount} waiting`,
            actionLabel: 'Review drivers',
            actionHref: path('dashboards.admin.driver-verification'),
        },
        'driver-verification': {
            eyebrow: 'Driver verification',
            title: 'Review submitted IDs',
            copy: 'Approve only driver profiles with both CIN sides available in local storage.',
            label: 'Pending reviews',
            value: `${pendingCount} waiting`,
            actionLabel: 'All users',
            actionHref: path('dashboards.admin.users'),
        },
        users: {
            eyebrow: 'Users',
            title: 'All users',
            copy: 'Scan account status, roles, contact details, and driver profile data.',
            label: 'Account status',
            value: `${metrics.active_users ?? 0} active · ${alerts.suspended_users ?? 0} suspended`,
            actionLabel: 'Ride activity',
            actionHref: path('dashboards.admin.rides'),
        },
        rides: {
            eyebrow: 'Rides',
            title: 'Ride activity',
            copy: 'Monitor recent rides, route status, pricing, and driver ownership.',
            label: 'Ride outcomes',
            value: `${metrics.completed_rides ?? 0} completed · ${metrics.cancelled_rides ?? 0} cancelled`,
            actionLabel: 'Search rides',
            actionHref: path('rides.search'),
        },
    }[section];

    return (
        <section className="rounded-2xl border border-slate-200 bg-white p-6 sm:p-7">
            <div className="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                <div>
                    <p className="text-sm font-medium text-slate-500">{header.eyebrow}</p>
                    <h1 className="mt-2 text-2xl font-semibold tracking-tight text-slate-950 sm:text-3xl">{header.title}</h1>
                    <p className="mt-2 max-w-2xl text-sm leading-6 text-slate-500">{header.copy}</p>
                </div>
                <div className="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <div className="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
                        <p className="font-medium text-slate-500">{header.label}</p>
                        <p className="mt-1 font-semibold text-slate-950">{header.value}</p>
                    </div>
                    <Link href={header.actionHref} className="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg bg-slate-950 px-4 py-3 text-sm font-semibold text-white">
                        {header.actionLabel}
                        <IconArrow />
                    </Link>
                </div>
            </div>
        </section>
    );
}

function Overview(props: AdminProps) {
    return (
        <>
            <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                {statCards.map((card) => <StatCard key={card.key} card={card} value={props.metrics[card.key] ?? 0} />)}
            </div>

            <div className="grid gap-6 xl:grid-cols-[minmax(0,1fr)_390px]">
                <section className="rounded-2xl border border-slate-200 bg-white p-6">
                    <div className="flex items-center justify-between gap-3">
                        <h2 className="text-lg font-semibold text-slate-950">Admin sections</h2>
                        <span className="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-500">Overview</span>
                    </div>
                    <div className="mt-5 grid gap-3 lg:grid-cols-3">
                        <OverviewLink route="dashboards.admin.driver-verification" icon={<IconShield />} title="Driver ID reviews" copy={`${props.pendingDriverProfiles.length} driver profile${props.pendingDriverProfiles.length === 1 ? '' : 's'} waiting for review.`} />
                        <OverviewLink route="dashboards.admin.users" icon={<IconUsers />} title="User records" copy={`${props.metrics.active_users ?? 0} active and ${props.alerts.suspended_users ?? 0} suspended accounts.`} />
                        <OverviewLink route="dashboards.admin.rides" icon={<IconCar />} title="Ride activity" copy={`${props.metrics.completed_rides ?? 0} completed and ${props.metrics.cancelled_rides ?? 0} cancelled rides.`} />
                    </div>
                </section>

                <aside className="space-y-5">
                    <AlertPanel alerts={props.alerts} pendingCount={props.pendingDriverProfiles.length} />
                    <RecentRidesPanel rides={props.rides} />
                </aside>
            </div>
        </>
    );
}

function StatCard({ card, value }: { card: { label: string; note: string; icon: string }; value: string | number }) {
    const icon = card.icon === 'users' ? <IconUsers /> : card.icon === 'shield' ? <IconShield /> : card.icon === 'check' ? <IconCheck /> : <IconCalendar />;

    return (
        <section className="rounded-2xl border border-slate-200 bg-white p-5">
            <div className="flex items-start justify-between gap-4">
                <div className="min-w-0">
                    <p className="truncate text-sm font-medium text-slate-500">{card.label}</p>
                    <p className="mt-2 text-3xl font-semibold tracking-tight text-slate-950">{value}</p>
                    <p className="mt-1 truncate text-sm text-slate-500">{card.note}</p>
                </div>
                <span className="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-slate-200 bg-slate-50 text-slate-600">
                    {icon}
                </span>
            </div>
        </section>
    );
}

function OverviewLink({ route, icon, title, copy }: { route: string; icon: ReactNode; title: string; copy: string }) {
    return (
        <Link href={path(route)} className="rounded-xl border border-slate-200 bg-slate-50 p-5 transition hover:border-brand-200 hover:bg-white">
            <span className="flex h-10 w-10 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600">{icon}</span>
            <h3 className="mt-4 text-base font-semibold text-slate-950">{title}</h3>
            <p className="mt-2 text-sm leading-6 text-slate-500">{copy}</p>
        </Link>
    );
}

function AlertPanel({ alerts, pendingCount }: { alerts: Record<string, number>; pendingCount: number }) {
    return (
        <section className="rounded-2xl border border-slate-200 bg-white p-6">
            <div className="flex items-center justify-between">
                <h2 className="text-lg font-semibold text-slate-950">Attention</h2>
                <span className="text-sm font-medium text-slate-500">Now</span>
            </div>
            <div className="mt-5 space-y-3">
                <AlertRow label="Pending driver reviews" value={pendingCount} tone="amber" />
                <AlertRow label="Suspended users" value={alerts.suspended_users ?? 0} tone="rose" />
                <AlertRow label="Cancelled rides" value={alerts.cancelled_rides ?? 0} tone="slate" />
            </div>
        </section>
    );
}

function AlertRow({ label, value, tone }: { label: string; value: number; tone: 'amber' | 'rose' | 'slate' }) {
    const dotClass = tone === 'amber' ? 'bg-amber-500' : tone === 'rose' ? 'bg-rose-500' : 'bg-slate-300';

    return (
        <div className="flex items-center justify-between gap-4 rounded-xl border border-slate-200 bg-white px-4 py-3">
            <div className="flex min-w-0 items-center gap-3">
                <span className={`h-2.5 w-2.5 shrink-0 rounded-full ${dotClass}`} />
                <p className="truncate text-sm font-semibold text-slate-700">{label}</p>
            </div>
            <p className="text-sm font-semibold text-slate-950">{value}</p>
        </div>
    );
}

function RecentRidesPanel({ rides }: { rides: Ride[] }) {
    return (
        <section className="rounded-2xl border border-slate-200 bg-white p-6">
            <div className="flex items-center justify-between">
                <h2 className="text-lg font-semibold text-slate-950">Recent rides</h2>
                <Link href={path('dashboards.admin.rides')} className="text-sm font-medium text-slate-500">View all</Link>
            </div>
            <div className="mt-5 space-y-3">
                {rides.slice(0, 3).map((ride) => <CompactRideRow key={ride.id} ride={ride} />)}
                {rides.length === 0 && <EmptyState title="No ride activity" message="Recent rides will appear here." compact />}
            </div>
        </section>
    );
}

function CompactRideRow({ ride }: { ride: Ride }) {
    return (
        <article className="rounded-xl border border-slate-200 bg-white p-4">
            <div className="flex items-start justify-between gap-3">
                <div className="min-w-0">
                    <p className="break-words font-semibold text-slate-950">{ride.departure_city?.name ?? 'Departure'} <span className="text-slate-400">-&gt;</span> {ride.arrival_city?.name ?? 'Arrival'}</p>
                    <p className="mt-1 text-sm font-semibold text-slate-500">{ride.driver?.name ?? 'Driver not listed'}</p>
                </div>
                <StatusChip status={ride.status} />
            </div>
        </article>
    );
}

function DriverVerification({ metrics, pendingDriverProfiles }: AdminProps) {
    return (
        <section className="rounded-2xl border border-slate-200 bg-white p-6">
            <div className="flex items-center justify-between gap-3">
                <div>
                    <h2 className="text-lg font-semibold text-slate-950">Submitted IDs</h2>
                    <p className="mt-1 text-sm leading-6 text-slate-500">{metrics.pending_driver_verifications} profile{metrics.pending_driver_verifications === 1 ? '' : 's'} waiting for CIN review.</p>
                </div>
                <span className="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">{pendingDriverProfiles.length} pending</span>
            </div>
            <div className="mt-5 space-y-4">
                {pendingDriverProfiles.length > 0 ? pendingDriverProfiles.map((profile) => <DriverProfileReview key={profile.id} profile={profile} />) : <EmptyState title="No pending driver profiles" message="New driver identity submissions will appear here." />}
            </div>
        </section>
    );
}

function DriverProfileReview({ profile }: { profile: DriverProfile & { user: UserSummary } }) {
    const form = useForm({});

    return (
        <article className="grid gap-5 rounded-xl border border-slate-200 bg-slate-50 p-5 lg:grid-cols-[minmax(320px,520px)_minmax(0,1fr)]">
            <div className="min-w-0">
                <div className="grid gap-3 sm:grid-cols-2">
                    <PhotoPreview label="Front" photo={profile.cin_front_photo} missing="Front photo unavailable" />
                    <PhotoPreview label="Back" photo={profile.cin_back_photo} missing="Back photo unavailable" />
                </div>
                {! profile.photos_complete && (
                    <div className="mt-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3">
                        <p className="text-xs font-semibold leading-5 text-amber-800">Verification needs both CIN sides. Approval stays disabled until the missing side is uploaded.</p>
                    </div>
                )}
            </div>
            <div className="min-w-0">
                <div className="flex h-full flex-col justify-between gap-5">
                    <div className="min-w-0">
                        <p className="font-semibold text-slate-950">{profile.user.name}</p>
                        <p className="mt-1 break-words text-sm font-semibold text-slate-500">{profile.user.email}</p>
                        <p className="mt-1 text-sm font-semibold text-slate-500">{profile.user.phone}</p>
                    </div>
                    <dl className="grid gap-3 text-sm text-slate-600 sm:grid-cols-2">
                        <Data label="CIN" value={profile.cin_number} />
                        <Data label="Vehicle" value={profile.vehicle ? `${profile.vehicle.brand} ${profile.vehicle.model}` : 'No vehicle'} />
                        <Data label="Submitted" value={profile.submitted_at ?? 'Not available'} />
                        <Data label="Front file" value={profile.cin_front_photo.exists ? 'Uploaded' : 'Missing locally'} />
                        <Data label="Back file" value={profile.cin_back_photo.exists ? 'Uploaded' : 'Missing locally'} />
                    </dl>
                    <button type="button" disabled={! profile.photos_complete || form.processing} onClick={() => form.patch(path('admin.driver-profiles.verify', profile.id))} className="rounded-lg bg-emerald-600 px-4 py-2.5 text-xs font-semibold text-white transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:bg-slate-300">Verify driver</button>
                </div>
            </div>
        </article>
    );
}

function Users({ metrics, alerts, users }: AdminProps) {
    return (
        <section className="rounded-2xl border border-slate-200 bg-white p-6">
            <div className="flex items-center justify-between gap-3">
                <div>
                    <h2 className="text-lg font-semibold text-slate-950">User records</h2>
                    <p className="mt-1 text-sm text-slate-500">{metrics.active_users} active · {alerts.suspended_users} suspended</p>
                </div>
                <span className="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-500">{users.length} total</span>
            </div>

            <div className="mt-5 hidden overflow-x-auto rounded-xl border border-slate-200 lg:block">
                <table className="min-w-full divide-y divide-slate-100 text-sm">
                    <thead className="bg-slate-50 text-left text-xs font-semibold text-slate-500">
                        <tr>
                            <th className="px-4 py-3">User</th>
                            <th className="px-4 py-3">Role</th>
                            <th className="px-4 py-3">Contact</th>
                            <th className="px-4 py-3">Identity</th>
                            <th className="px-4 py-3">Status</th>
                            <th className="px-4 py-3">Data</th>
                            <th className="px-4 py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y divide-slate-100 bg-white">
                        {users.map((user) => <UserRow key={user.id} user={user} />)}
                    </tbody>
                </table>
            </div>

            <div className="mt-5 space-y-3 lg:hidden">
                {users.map((user) => <UserMobileCard key={user.id} user={user} />)}
            </div>
        </section>
    );
}

function Rides({ metrics, rides }: AdminProps) {
    return (
        <section className="rounded-2xl border border-slate-200 bg-white p-6">
            <div className="flex items-center justify-between gap-3">
                <div>
                    <h2 className="text-lg font-semibold text-slate-950">Ride records</h2>
                    <p className="mt-1 text-sm text-slate-500">{metrics.completed_rides} completed · {metrics.cancelled_rides} cancelled</p>
                </div>
                <span className="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-500">{rides.length} shown</span>
            </div>

            <div className="mt-5 grid gap-3 lg:grid-cols-2">
                {rides.map((ride) => <RideCard key={ride.id} ride={ride} />)}
                {rides.length === 0 && <EmptyState title="No ride activity" message="Recent ride records will appear here." />}
            </div>
        </section>
    );
}

function RideCard({ ride }: { ride: Ride }) {
    return (
        <article className="rounded-xl border border-slate-200 bg-slate-50 p-5">
            <div className="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div className="flex min-w-0 gap-4">
                    <span className="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600">
                        <IconCar />
                    </span>
                    <div className="min-w-0">
                        <h3 className="break-words text-lg font-semibold leading-tight text-slate-950">{ride.departure_city?.name ?? 'Departure'} <span className="text-slate-300">→</span> {ride.arrival_city?.name ?? 'Arrival'}</h3>
                        <p className="mt-2 text-sm font-medium text-slate-500">{ride.departure_datetime_label}</p>
                        <p className="mt-1 text-sm font-medium text-slate-500">Driver: {ride.driver?.name ?? 'Not listed'}</p>
                    </div>
                </div>
                <StatusChip status={ride.status} />
            </div>
            <div className="mt-4 grid gap-2 border-t border-slate-200 pt-4 text-sm sm:grid-cols-3">
                <Data label="Seats" value={ride.available_seats_label} />
                <Data label="Price" value={ride.price_label} />
                <Data label="Vehicle" value={ride.vehicle ? `${ride.vehicle.brand} ${ride.vehicle.model}` : 'Not listed'} />
            </div>
            <RideModerationForm ride={ride} />
        </article>
    );
}

function RideModerationForm({ ride }: { ride: Ride }) {
    const form = useForm({
        status: ride.status,
        admin_note: ride.admin_note ?? '',
    });

    return (
        <form
            onSubmit={(event) => {
                event.preventDefault();
                form.patch(path('admin.rides.moderate', ride.id));
            }}
            className="mt-4 grid gap-3 border-t border-slate-200 pt-4 sm:grid-cols-[180px_minmax(0,1fr)_auto]"
        >
            <select value={form.data.status === 'scheduled' ? '' : form.data.status} onChange={(event) => form.setData('status', event.target.value)} className="rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 outline-none">
                <option value="" disabled>Moderate</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>
            <input value={form.data.admin_note} onChange={(event) => form.setData('admin_note', event.target.value)} placeholder="Admin note" className="rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 outline-none" />
            <button type="submit" disabled={form.processing} className="rounded-lg bg-slate-950 px-4 py-2 text-xs font-semibold text-white transition hover:bg-slate-800 disabled:opacity-50">Save</button>
        </form>
    );
}

function PhotoPreview({ label, photo, missing }: { label: string; photo: { url: string | null; path: string | null; exists: boolean }; missing: string }) {
    return (
        <div>
            <p className="mb-2 text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">{label}</p>
            <div className="aspect-[4/3] overflow-hidden rounded-xl border border-slate-200 bg-white">
                {photo.exists && photo.url ? (
                    <a href={photo.url} target="_blank" rel="noreferrer"><img src={photo.url} alt={`CIN ${label.toLowerCase()} photo`} className="h-full w-full object-cover" /></a>
                ) : (
                    <div className="flex h-full items-center justify-center bg-white px-6 text-center">
                        <div>
                            <p className="text-sm font-semibold text-rose-600">{missing}</p>
                            {photo.path && <p className="mt-2 break-all text-xs font-semibold text-slate-400">{photo.path}</p>}
                        </div>
                    </div>
                )}
            </div>
        </div>
    );
}

function UserRow({ user }: { user: UserSummary }) {
    const profile = user.driver_profile;

    return (
        <tr className="align-top">
            <td className="px-4 py-3"><p className="font-semibold text-slate-900">{user.name}</p><p className="text-xs text-slate-500">Joined {user.joined_date}</p></td>
            <td className="px-4 py-3 text-slate-600">{formatRole(user.role)}</td>
            <td className="px-4 py-3 text-slate-600"><p>{user.email}</p><p className="mt-1 text-xs text-slate-500">{user.phone}</p></td>
            <td className="px-4 py-3 text-slate-600">{profile ? <><p>{profile.cin_verified ? 'Verified' : 'Pending'}</p><p className="mt-1 text-xs text-slate-500">CIN {profile.cin_number}</p></> : <span className="text-slate-400">No driver profile</span>}</td>
            <td className="px-4 py-3"><StatusChip status={user.account_status ?? 'active'} /></td>
            <td className="px-4 py-3"><UserDetails user={user} /></td>
            <td className="px-4 py-3"><UserAdminActions user={user} /></td>
        </tr>
    );
}

function UserMobileCard({ user }: { user: UserSummary }) {
    const profile = user.driver_profile;

    return (
        <article className="rounded-xl border border-slate-200 bg-white p-4">
            <div className="flex items-start justify-between gap-3">
                <div className="min-w-0">
                    <p className="break-words font-semibold text-slate-950">{user.name}</p>
                    <p className="mt-1 text-sm font-semibold text-slate-500">{formatRole(user.role)} · {user.email}</p>
                </div>
                <StatusChip status={user.account_status ?? 'active'} />
            </div>
            <div className="mt-4 rounded-lg border border-slate-200 bg-slate-50 px-3 py-3 text-sm text-slate-600">
                {profile ? <p>{profile.cin_verified ? 'Verified driver' : 'Pending driver'} · CIN {profile.cin_number}</p> : <p>No driver profile</p>}
            </div>
            <div className="mt-4">
                <UserDetails user={user} />
            </div>
            <div className="mt-4">
                <UserAdminActions user={user} />
            </div>
        </article>
    );
}

function UserAdminActions({ user }: { user: UserSummary }) {
    const form = useForm({});
    const isSuspended = user.account_status === 'suspended';

    return (
        <button
            type="button"
            onClick={() => form.patch(path(isSuspended ? 'admin.users.activate' : 'admin.users.suspend', user.id))}
            disabled={form.processing}
            className={`rounded-lg px-4 py-2 text-xs font-semibold transition disabled:opacity-50 ${isSuspended ? 'bg-emerald-600 text-white hover:bg-emerald-700' : 'bg-rose-50 text-rose-700 hover:bg-rose-100'}`}
        >
            {isSuspended ? 'Activate' : 'Suspend'}
        </button>
    );
}

function UserDetails({ user }: { user: UserSummary }) {
    const profile = user.driver_profile;

    return (
        <details className="group w-full lg:w-72 lg:max-w-[70vw]">
            <summary className="cursor-pointer rounded-lg bg-slate-100 px-4 py-2 text-xs font-semibold text-slate-700 transition hover:bg-brand-50 hover:text-brand-700">View data</summary>
            <dl className="mt-3 grid gap-2 rounded-xl border border-slate-200 bg-slate-50 p-4 text-xs text-slate-600">
                <Data label="User ID" value={String(user.id)} />
                <Data label="Email verified" value={user.email_verified ? 'Yes' : 'No'} />
                <Data label="Phone verified" value={user.phone_verified ? 'Yes' : 'No'} />
                <Data label="Suspended at" value={user.suspended_at ?? 'Not suspended'} />
                <Data label="Driver rating" value={profile?.avg_rating ?? 'N/A'} />
                <Data label="Driver trips" value={profile ? String(profile.total_trips) : 'N/A'} />
                <div><dt className="font-semibold text-slate-900">CIN front photo</dt><dd>{profile?.cin_front_photo.url ? <a href={profile.cin_front_photo.url} target="_blank" rel="noreferrer" className="font-semibold text-brand-700 hover:text-brand-800">Open front</a> : 'Not provided'}</dd></div>
                <div><dt className="font-semibold text-slate-900">CIN back photo</dt><dd>{profile?.cin_back_photo.url ? <a href={profile.cin_back_photo.url} target="_blank" rel="noreferrer" className="font-semibold text-brand-700 hover:text-brand-800">Open back</a> : 'Not provided'}</dd></div>
                <Data label="Vehicle" value={profile?.vehicle ? `${profile.vehicle.brand} ${profile.vehicle.model}` : 'N/A'} />
            </dl>
        </details>
    );
}

function Data({ label, value }: { label: string; value: string }) {
    return <div className="min-w-0 rounded-lg bg-white px-3 py-3"><dt className="font-semibold text-slate-900">{label}</dt><dd className="mt-1 break-words">{value}</dd></div>;
}

function EmptyState({ title, message, compact = false }: { title: string; message: string; compact?: boolean }) {
    return (
        <div className={`mt-5 rounded-[1rem] border border-dashed border-slate-200 bg-slate-50 text-center ${compact ? 'px-4 py-8' : 'px-6 py-12'}`}>
            <div className="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-50 text-brand-700">
                <IconCalendar />
            </div>
            <h3 className="mt-4 text-base font-semibold text-slate-950">{title}</h3>
            <p className="mx-auto mt-2 max-w-sm text-sm leading-6 text-slate-500">{message}</p>
        </div>
    );
}

function formatRole(role: string) {
    return role.replace(/^\w/, (char) => char.toUpperCase());
}
