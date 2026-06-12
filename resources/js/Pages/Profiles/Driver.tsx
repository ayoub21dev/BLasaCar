import { Link } from '@inertiajs/react';
import { Layout } from '../../components/Layout';
import { StatusChip } from '../../components/ui';
import { path } from '../../routes';
import { ProfileUserSummary, PublicDriverProfile, ReviewSummary, Ride } from '../../types';

type DriverProfileProps = {
    driver: ProfileUserSummary;
    profile: PublicDriverProfile;
    stats: Record<string, number>;
    reviews: ReviewSummary[];
    rides: Ride[];
};

// Shows a public driver profile with reviews and available rides.
export default function DriverProfilePage({ driver, profile, stats, reviews, rides }: DriverProfileProps) {
    return (
        <Layout title={`${driver.name} profile`}>
            <section className="bg-slate-50 py-8 sm:py-12">
                <div className="shell space-y-6">
                    <Link href={path('rides.search')} className="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-bold text-slate-600 transition hover:border-brand-200 hover:text-brand-700">
                        Back to rides
                    </Link>

                    <div className="grid gap-6 lg:grid-cols-[minmax(0,1fr)_360px]">
                        <div className="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                            <div className="flex flex-col gap-5 sm:flex-row sm:items-start">
                                {driver.profile_photo_url ? (
                                    <img src={driver.profile_photo_url} alt={driver.name} className="h-20 w-20 shrink-0 rounded-2xl object-cover shadow-sm" />
                                ) : (
                                    <div className="flex h-20 w-20 shrink-0 items-center justify-center rounded-2xl bg-slate-950 text-2xl font-black uppercase text-white">
                                        {driver.initials ?? driver.name.slice(0, 2)}
                                    </div>
                                )}
                                <div className="min-w-0 flex-1">
                                    <div className="flex flex-wrap items-center gap-2">
                                        <span className="rounded-full bg-brand-50 px-3 py-1 text-xs font-black uppercase tracking-[0.14em] text-brand-700">Driver profile</span>
                                        <VerifiedBadge verified={profile.cin_verified} />
                                    </div>
                                    <h1 className="mt-4 break-words text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">{driver.name}</h1>
                                    <p className="mt-3 text-sm font-semibold text-slate-500">Joined {driver.joined_date ?? 'recently'} · {profile.total_trips} completed trip{profile.total_trips === 1 ? '' : 's'}</p>
                                    <div className="mt-5 grid gap-3 sm:grid-cols-3">
                                        <Stat label="Rating" value={`${profile.avg_rating}/5`} />
                                        <Stat label="Reviews" value={String(stats.review_count ?? 0)} />
                                        <Stat label="Upcoming" value={String(stats.scheduled_rides ?? 0)} />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <aside className="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                            <p className="text-xs font-black uppercase tracking-[0.14em] text-slate-400">Vehicle</p>
                            {profile.vehicles.length > 0 ? (
                                <div className="mt-4 space-y-3">
                                    {profile.vehicles.map((vehicle) => (
                                        <div key={vehicle.id} className="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                                            <p className="font-black text-slate-950">{vehicle.brand}</p>
                                            <p className="mt-1 text-sm font-semibold text-slate-500">{vehicle.model}</p>
                                        </div>
                                    ))}
                                </div>
                            ) : (
                                <p className="mt-4 text-sm font-semibold text-slate-500">No vehicle listed yet.</p>
                            )}
                            <div className="mt-5 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                                <p className="text-xs font-bold uppercase tracking-[0.14em] text-slate-400">Trust checks</p>
                                <p className="mt-2 text-sm font-semibold text-slate-700">{driver.phone_verified ? 'Phone verified' : 'Phone not verified'} · {driver.email_verified ? 'Email verified' : 'Email not verified'}</p>
                            </div>
                        </aside>
                    </div>

                    <div className="grid gap-6 xl:grid-cols-[minmax(0,1fr)_420px]">
                        <section className="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                            <div className="flex items-center justify-between gap-3">
                                <h2 className="text-xl font-black text-slate-950">Traveler reviews</h2>
                                <span className="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-500">{reviews.length} shown</span>
                            </div>
                            {reviews.length > 0 ? (
                                <div className="mt-5 space-y-3">
                                    {reviews.map((review) => <ReviewCard key={review.id} review={review} />)}
                                </div>
                            ) : (
                                <EmptyState title="No comments yet" message="Completed-trip comments will appear here after travelers review this driver." />
                            )}
                        </section>

                        <section className="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                            <h2 className="text-xl font-black text-slate-950">Available rides</h2>
                            {rides.length > 0 ? (
                                <div className="mt-5 space-y-3">
                                    {rides.map((ride) => <RideCard key={ride.id} ride={ride} />)}
                                </div>
                            ) : (
                                <EmptyState title="No available rides" message="This driver has no future ride with open seats right now." compact />
                            )}
                        </section>
                    </div>
                </div>
            </section>
        </Layout>
    );
}

// Renders the driver's identity verification badge.
function VerifiedBadge({ verified }: { verified: boolean }) {
    return (
        <span className={`rounded-full px-3 py-1 text-xs font-black ${verified ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700'}`}>
            {verified ? 'ID verified' : 'Verification pending'}
        </span>
    );
}

// Renders one driver profile statistic.
function Stat({ label, value }: { label: string; value: string }) {
    return (
        <div className="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
            <p className="text-xs font-bold uppercase tracking-[0.14em] text-slate-400">{label}</p>
            <p className="mt-1 text-lg font-black text-slate-950">{value}</p>
        </div>
    );
}

// Renders one traveler review on the driver profile.
function ReviewCard({ review }: { review: ReviewSummary }) {
    return (
        <article className="rounded-xl border border-slate-200 bg-white p-4">
            <div className="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div className="min-w-0">
                    <p className="font-black text-slate-950">{review.traveler?.name ?? 'Traveler'}</p>
                    <p className="mt-1 text-sm font-semibold text-slate-500">{review.ride?.route ?? 'Completed ride'} · {review.created_label ?? 'recently'}</p>
                </div>
                <span className="shrink-0 rounded-full bg-amber-50 px-3 py-1 text-xs font-black text-amber-700">{review.rating}/5</span>
            </div>
            <p className="mt-3 text-sm leading-6 text-slate-600">{review.comment || 'No written comment.'}</p>
        </article>
    );
}

// Renders one available ride on the driver profile.
function RideCard({ ride }: { ride: Ride }) {
    return (
        <article className="rounded-xl border border-slate-200 bg-slate-50 p-4">
            <div className="flex items-start justify-between gap-3">
                <div className="min-w-0">
                    <p className="break-words font-black text-slate-950">{ride.departure_city?.name ?? 'Departure'} -&gt; {ride.arrival_city?.name ?? 'Arrival'}</p>
                    <p className="mt-1 text-sm font-semibold text-slate-500">{ride.departure_datetime_label}</p>
                </div>
                <StatusChip status={ride.status} />
            </div>
            <div className="mt-4 flex flex-wrap items-center justify-between gap-3">
                <p className="text-sm font-black text-brand-700">{ride.price_label} · {ride.available_seats_label}</p>
                <Link href={path('rides.show', ride.id)} className="rounded-lg bg-slate-950 px-4 py-2 text-xs font-black text-white">View ride</Link>
            </div>
        </article>
    );
}

// Shows a profile empty state.
function EmptyState({ title, message, compact = false }: { title: string; message: string; compact?: boolean }) {
    return (
        <div className={`mt-5 rounded-xl border border-dashed border-slate-200 bg-slate-50 px-5 text-center ${compact ? 'py-8' : 'py-12'}`}>
            <h3 className="text-base font-black text-slate-950">{title}</h3>
            <p className="mx-auto mt-2 max-w-sm text-sm leading-6 text-slate-500">{message}</p>
        </div>
    );
}
