import { Link, useForm, usePage } from '@inertiajs/react';
import { DashboardSidebar, StatusChip } from '../../components/ui';
import { Layout } from '../../components/Layout';
import { path } from '../../routes';
import { DriverProfile, Ride, SharedProps, UserSummary } from '../../types';

type AdminProps = {
    section: 'overview' | 'driver-verification' | 'users' | 'rides';
    metrics: Record<string, number>;
    alerts: Record<string, number>;
    users: UserSummary[];
    rides: Ride[];
    pendingDriverProfiles: Array<DriverProfile & { user: UserSummary }>;
};

const sidebarItems = [
    ['Overview', 'dashboards.admin', '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="9"/><rect x="14" y="3" width="7" height="5"/><rect x="14" y="12" width="7" height="9"/><rect x="3" y="16" width="7" height="5"/></svg>'],
    ['Driver verification', 'dashboards.admin.driver-verification', '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4"/><path d="M12 3 4 7v5c0 5 3.4 8.3 8 9 4.6-.7 8-4 8-9V7l-8-4Z"/></svg>'],
    ['All users', 'dashboards.admin.users', '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>'],
    ['Ride activity', 'dashboards.admin.rides', '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 17h2l-1.4-4.2A3 3 0 0 0 16.8 11H7.2a3 3 0 0 0-2.8 1.8L3 17h2"/><circle cx="7" cy="17" r="2"/><circle cx="17" cy="17" r="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>'],
    ['Search rides', 'rides.search', '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>'],
].map(([label, route, icon]) => ({ label, route, icon }));

export default function Admin(props: AdminProps) {
    const { flash, errors } = usePage<SharedProps>().props;

    return (
        <Layout title="Admin Dashboard" showFooter={false}>
            <section className="py-10">
                <div className="shell page-enter">
                    <div className="flex gap-8">
                        <DashboardSidebar label="Admin dashboard" items={sidebarItems} />
                        <div className="min-w-0 flex-1 space-y-6">
                            {flash.status && <div className="rounded-[1.25rem] border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-700">{flash.status}</div>}
                            {errors.driver_profile && <div className="rounded-[1.25rem] border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-bold text-rose-700">{errors.driver_profile}</div>}
                            {props.section === 'overview' && <Overview {...props} />}
                            {props.section === 'driver-verification' && <DriverVerification {...props} />}
                            {props.section === 'users' && <Users {...props} />}
                            {props.section === 'rides' && <Rides {...props} />}
                        </div>
                    </div>
                </div>
            </section>
        </Layout>
    );
}

function Overview({ metrics, alerts, pendingDriverProfiles }: AdminProps) {
    return (
        <>
            <div className="surface p-6 sm:p-8">
                <div className="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p className="text-sm font-semibold uppercase tracking-[0.18em] text-brand-600">Platform overview</p>
                        <h1 className="mt-2 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">Admin workspace</h1>
                        <p className="mt-3 max-w-2xl text-slate-500">Review driver identities, inspect users, and monitor ride activity from one place.</p>
                    </div>
                    <Link href={path('dashboards.admin.driver-verification')} className="brand-button-secondary text-sm">Review drivers</Link>
                </div>
            </div>
            <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <AdminStat label="Total users" value={metrics.total_users} />
                <AdminStat label="Pending ID checks" value={metrics.pending_driver_verifications} />
                <AdminStat label="Verified drivers" value={metrics.verified_drivers} />
                <AdminStat label="Bookings" value={metrics.total_bookings} />
            </div>
            <div className="grid gap-4 lg:grid-cols-3">
                <OverviewLink route="dashboards.admin.driver-verification" eyebrow="Verification" title="Driver ID reviews" copy={`${pendingDriverProfiles.length} driver profile${pendingDriverProfiles.length === 1 ? '' : 's'} waiting for review.`} />
                <OverviewLink route="dashboards.admin.users" eyebrow="Users" title="User records" copy={`${metrics.active_users} active and ${alerts.suspended_users} suspended accounts.`} />
                <OverviewLink route="dashboards.admin.rides" eyebrow="Rides" title="Ride activity" copy={`${metrics.completed_rides} completed and ${metrics.cancelled_rides} cancelled rides.`} />
            </div>
        </>
    );
}

function DriverVerification({ metrics, pendingDriverProfiles }: AdminProps) {
    return (
        <div className="dashboard-panel">
            <div className="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p className="text-sm font-semibold uppercase tracking-[0.18em] text-brand-600">Driver verification</p>
                    <h1 className="mt-2 text-3xl font-black tracking-tight text-slate-950">Review submitted IDs</h1>
                    <p className="mt-2 text-sm leading-6 text-slate-500">{metrics.pending_driver_verifications} profile{metrics.pending_driver_verifications === 1 ? '' : 's'} waiting for CIN review.</p>
                </div>
                <span className="w-fit rounded-full bg-amber-100 px-3 py-1 text-xs font-black text-amber-700">{pendingDriverProfiles.length} pending</span>
            </div>
            <div className="mt-6 space-y-4">
                {pendingDriverProfiles.length > 0 ? pendingDriverProfiles.map((profile) => <DriverProfileReview key={profile.id} profile={profile} />) : <p className="rounded-[1.25rem] bg-slate-50 px-4 py-4 text-sm font-semibold text-slate-500">No pending driver profiles.</p>}
            </div>
        </div>
    );
}

function DriverProfileReview({ profile }: { profile: DriverProfile & { user: UserSummary } }) {
    const form = useForm({});
    return (
        <div className="grid gap-5 rounded-[1.5rem] border border-slate-200 bg-slate-50 p-5 lg:grid-cols-[minmax(320px,520px)_minmax(0,1fr)]">
            <div className="min-w-0">
                <div className="grid gap-3 sm:grid-cols-2">
                    <PhotoPreview label="Front" photo={profile.cin_front_photo} missing="Front photo unavailable" />
                    <PhotoPreview label="Back" photo={profile.cin_back_photo} missing="Back photo unavailable" />
                </div>
                {! profile.photos_complete && (
                    <div className="mt-3 rounded-[1rem] border border-amber-200 bg-amber-50 px-4 py-3">
                        <p className="text-xs font-bold leading-5 text-amber-800">Verification needs both CIN sides. Existing one-photo submissions can be reviewed visually, but approval stays disabled until the missing side is uploaded.</p>
                    </div>
                )}
            </div>
            <div className="min-w-0">
                <div className="flex h-full flex-col justify-between gap-5">
                    <div className="min-w-0">
                        <p className="font-bold text-slate-950">{profile.user.name}</p>
                        <p className="mt-1 break-words text-sm font-semibold text-slate-500">{profile.user.email}</p>
                        <p className="mt-1 text-sm font-semibold text-slate-500">{profile.user.phone}</p>
                    </div>
                    <dl className="grid gap-3 text-sm text-slate-600 sm:grid-cols-2">
                        <Data label="CIN" value={profile.cin_number} />
                        <Data label="Vehicle" value={profile.vehicle ? `${profile.vehicle.brand} ${profile.vehicle.model}` : 'No vehicle'} />
                        <Data label="Submitted" value={profile.submitted_at ?? ''} />
                        <Data label="Front file" value={profile.cin_front_photo.exists ? 'Uploaded' : 'Missing locally'} />
                        <Data label="Back file" value={profile.cin_back_photo.exists ? 'Uploaded' : 'Missing locally'} />
                    </dl>
                    <button type="button" disabled={! profile.photos_complete || form.processing} onClick={() => form.patch(path('admin.driver-profiles.verify', profile.id))} className="rounded-full bg-emerald-600 px-4 py-2 text-xs font-black text-white transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:bg-slate-300">Verify driver</button>
                </div>
            </div>
        </div>
    );
}

function Users({ metrics, alerts, users }: AdminProps) {
    return (
        <div className="dashboard-panel">
            <div className="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p className="text-sm font-semibold uppercase tracking-[0.18em] text-brand-600">Users</p>
                    <h1 className="mt-2 text-3xl font-black tracking-tight text-slate-950">All users</h1>
                    <p className="mt-2 text-sm text-slate-500">{metrics.active_users} active &middot; {alerts.suspended_users} suspended</p>
                </div>
            </div>
            <div className="mt-6 overflow-x-auto rounded-[1.5rem] border border-slate-200">
                <table className="min-w-full divide-y divide-slate-200 text-sm">
                    <thead className="bg-slate-50 text-left text-slate-500"><tr><th className="px-5 py-4 font-semibold">User</th><th className="px-5 py-4 font-semibold">Role</th><th className="px-5 py-4 font-semibold">Contact</th><th className="px-5 py-4 font-semibold">Identity</th><th className="px-5 py-4 font-semibold">Status</th><th className="px-5 py-4 font-semibold">Data</th></tr></thead>
                    <tbody className="divide-y divide-slate-100 bg-white">{users.map((user) => <UserRow key={user.id} user={user} />)}</tbody>
                </table>
            </div>
        </div>
    );
}

function Rides({ metrics, rides }: AdminProps) {
    return (
        <div className="dashboard-panel">
            <div className="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p className="text-sm font-semibold uppercase tracking-[0.18em] text-brand-600">Rides</p>
                    <h1 className="mt-2 text-3xl font-black tracking-tight text-slate-950">Ride activity</h1>
                    <p className="mt-2 text-sm text-slate-500">{metrics.completed_rides} completed &middot; {metrics.cancelled_rides} cancelled</p>
                </div>
            </div>
            <div className="mt-6 grid gap-4 lg:grid-cols-2">
                {rides.map((ride) => (
                    <div key={ride.id} className="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-5">
                        <div className="flex items-start justify-between gap-3">
                            <div>
                                <p className="font-semibold text-slate-900">{ride.departure_city?.name} &rarr; {ride.arrival_city?.name}</p>
                                <p className="mt-1 text-sm text-slate-500">{ride.departure_datetime_label}</p>
                            </div>
                            <StatusChip status={ride.status} />
                        </div>
                        <div className="mt-4 text-sm text-slate-600">Driver: {ride.driver?.name}</div>
                    </div>
                ))}
            </div>
        </div>
    );
}

function PhotoPreview({ label, photo, missing }: { label: string; photo: { url: string | null; path: string | null; exists: boolean }; missing: string }) {
    return (
        <div>
            <p className="mb-2 text-xs font-black uppercase tracking-[0.16em] text-slate-500">{label}</p>
            <div className="aspect-[4/3] overflow-hidden rounded-[1.25rem] border border-slate-200 bg-white">
                {photo.exists && photo.url ? (
                    <a href={photo.url} target="_blank" rel="noreferrer"><img src={photo.url} alt={`CIN ${label.toLowerCase()} photo`} className="h-full w-full object-cover" /></a>
                ) : (
                    <div className="flex h-full items-center justify-center bg-white px-6 text-center">
                        <div>
                            <p className="text-sm font-black text-rose-600">{missing}</p>
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
            <td className="px-5 py-4"><p className="font-semibold text-slate-900">{user.name}</p><p className="text-xs text-slate-500">Joined {user.joined_date}</p></td>
            <td className="px-5 py-4 text-slate-600">{user.role[0].toUpperCase() + user.role.slice(1)}</td>
            <td className="px-5 py-4 text-slate-600"><p>{user.email}</p><p className="mt-1 text-xs text-slate-500">{user.phone}</p></td>
            <td className="px-5 py-4 text-slate-600">{profile ? <><p>{profile.cin_verified ? 'Verified' : 'Pending'}</p><p className="mt-1 text-xs text-slate-500">CIN {profile.cin_number}</p></> : <span className="text-slate-400">No driver profile</span>}</td>
            <td className="px-5 py-4"><StatusChip status={user.account_status ?? 'active'} /></td>
            <td className="px-5 py-4">
                <details className="group w-72 max-w-[70vw]">
                    <summary className="cursor-pointer rounded-full bg-slate-100 px-4 py-2 text-xs font-black text-slate-700 transition hover:bg-brand-50 hover:text-brand-700">View data</summary>
                    <dl className="mt-3 grid gap-2 rounded-[1.25rem] border border-slate-200 bg-slate-50 p-4 text-xs text-slate-600">
                        <Data label="User ID" value={String(user.id)} />
                        <Data label="Email verified" value={user.email_verified ? 'Yes' : 'No'} />
                        <Data label="Phone verified" value={user.phone_verified ? 'Yes' : 'No'} />
                        <Data label="Suspended at" value={user.suspended_at ?? 'Not suspended'} />
                        <Data label="Driver rating" value={profile?.avg_rating ?? 'N/A'} />
                        <Data label="Driver trips" value={profile ? String(profile.total_trips) : 'N/A'} />
                        <div><dt className="font-bold text-slate-900">CIN front photo</dt><dd>{profile?.cin_front_photo.url ? <a href={profile.cin_front_photo.url} target="_blank" rel="noreferrer" className="font-bold text-brand-700 hover:text-brand-800">Open front</a> : 'Not provided'}</dd></div>
                        <div><dt className="font-bold text-slate-900">CIN back photo</dt><dd>{profile?.cin_back_photo.url ? <a href={profile.cin_back_photo.url} target="_blank" rel="noreferrer" className="font-bold text-brand-700 hover:text-brand-800">Open back</a> : 'Not provided'}</dd></div>
                        <Data label="Vehicle" value={profile?.vehicle ? `${profile.vehicle.brand} ${profile.vehicle.model}` : 'N/A'} />
                    </dl>
                </details>
            </td>
        </tr>
    );
}

function AdminStat({ label, value }: { label: string; value: number }) {
    return <div className="stat-tile"><p className="text-sm text-slate-500">{label}</p><p className="mt-2 text-3xl font-bold text-slate-950">{value}</p></div>;
}

function OverviewLink({ route, eyebrow, title, copy }: { route: string; eyebrow: string; title: string; copy: string }) {
    return <Link href={path(route)} className="dashboard-panel block transition hover:border-brand-200"><p className="text-sm font-black uppercase tracking-[0.14em] text-brand-600">{eyebrow}</p><h2 className="mt-3 text-xl font-bold text-slate-950">{title}</h2><p className="mt-2 text-sm leading-6 text-slate-500">{copy}</p></Link>;
}

function Data({ label, value }: { label: string; value: string }) {
    return <div className="rounded-[1rem] bg-white px-4 py-3"><dt className="font-bold text-slate-900">{label}</dt><dd className="mt-1 break-words">{value}</dd></div>;
}
