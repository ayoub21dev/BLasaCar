import { Link } from '@inertiajs/react';
import { Layout } from '../../components/Layout';
import { StatusChip } from '../../components/ui';
import { path } from '../../routes';
import { Booking, BookingContact, ProfileUserSummary, ReviewSummary } from '../../types';

type TravelerProfileProps = {
    traveler: ProfileUserSummary;
    stats: Record<string, number>;
    bookings: Booking[];
    reviews: ReviewSummary[];
};

// Shows a traveler profile to drivers who have bookings with that traveler.
export default function TravelerProfilePage({ traveler, stats, bookings, reviews }: TravelerProfileProps) {
    return (
        <Layout title={`${traveler.name} profile`}>
            <section className="bg-slate-50 py-8 sm:py-12">
                <div className="shell space-y-6">
                    <Link href={path('dashboards.driver')} className="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-bold text-slate-600 transition hover:border-brand-200 hover:text-brand-700">
                        Back to driver dashboard
                    </Link>

                    <div className="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                        <div className="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                            <div className="flex min-w-0 flex-col gap-5 sm:flex-row sm:items-start">
                                {traveler.profile_photo_url ? (
                                    <img src={traveler.profile_photo_url} alt={traveler.name} className="h-20 w-20 shrink-0 rounded-2xl object-cover shadow-sm" />
                                ) : (
                                    <div className="flex h-20 w-20 shrink-0 items-center justify-center rounded-2xl bg-slate-950 text-2xl font-black uppercase text-white">
                                        {traveler.initials ?? traveler.name.slice(0, 2)}
                                    </div>
                                )}
                                <div className="min-w-0">
                                    <span className="rounded-full bg-brand-50 px-3 py-1 text-xs font-black uppercase tracking-[0.14em] text-brand-700">Traveler profile</span>
                                    <h1 className="mt-4 break-words text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">{traveler.name}</h1>
                                    <p className="mt-3 text-sm font-semibold text-slate-500">Joined {traveler.joined_date ?? 'recently'} · {traveler.phone_verified ? 'Phone verified' : 'Phone not verified'} · {traveler.email_verified ? 'Email verified' : 'Email not verified'}</p>
                                </div>
                            </div>
                            <div className="grid gap-3 sm:grid-cols-2 lg:w-[440px]">
                                <Stat label="Requests" value={String(stats.total_bookings ?? 0)} />
                                <Stat label="Active" value={String(stats.active_bookings ?? 0)} />
                                <Stat label="Completed" value={String(stats.completed_trips ?? 0)} />
                                <Stat label="Cancelled" value={String(stats.cancelled_trips ?? 0)} />
                            </div>
                        </div>
                    </div>

                    <div className="grid gap-6 xl:grid-cols-[minmax(0,1fr)_420px]">
                        <section className="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                            <div className="flex items-center justify-between gap-3">
                                <h2 className="text-xl font-black text-slate-950">Bookings with you</h2>
                                <span className="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-500">{stats.seats_reserved ?? 0} seat{(stats.seats_reserved ?? 0) === 1 ? '' : 's'} reserved</span>
                            </div>
                            <div className="mt-5 space-y-3">
                                {bookings.map((booking) => <BookingCard key={booking.id} booking={booking} />)}
                            </div>
                        </section>

                        <section className="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                            <h2 className="text-xl font-black text-slate-950">Reviews they left</h2>
                            {reviews.length > 0 ? (
                                <div className="mt-5 space-y-3">
                                    {reviews.map((review) => <ReviewCard key={review.id} review={review} />)}
                                </div>
                            ) : (
                                <EmptyState title="No review from this traveler" message="If they review a completed trip with you, the comment will appear here." />
                            )}
                        </section>
                    </div>
                </div>
            </section>
        </Layout>
    );
}

// Renders one traveler profile statistic.
function Stat({ label, value }: { label: string; value: string }) {
    return (
        <div className="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
            <p className="text-xs font-bold uppercase tracking-[0.14em] text-slate-400">{label}</p>
            <p className="mt-1 text-lg font-black text-slate-950">{value}</p>
        </div>
    );
}

// Renders one booking shared between the current driver and this traveler.
function BookingCard({ booking }: { booking: Booking }) {
    const ride = booking.ride;

    return (
        <article className="rounded-xl border border-slate-200 bg-slate-50 p-4">
            <div className="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div className="min-w-0">
                    <p className="break-words font-black text-slate-950">{ride?.departure_city?.name ?? 'Departure'} -&gt; {ride?.arrival_city?.name ?? 'Arrival'}</p>
                    <p className="mt-1 text-sm font-semibold text-slate-500">{ride?.departure_datetime_label ?? 'Date not set'} · {booking.seats_reserved} seat{booking.seats_reserved === 1 ? '' : 's'}</p>
                </div>
                <StatusChip status={booking.status} />
            </div>
            <ContactBlock contact={booking.traveler_contact} />
        </article>
    );
}

// Shows traveler contact only after an accepted booking unlocks it.
function ContactBlock({ contact }: { contact?: BookingContact | null }) {
    if (! contact) {
        return (
            <div className="mt-4 rounded-lg border border-slate-200 bg-white px-4 py-3">
                <p className="text-xs font-bold uppercase tracking-[0.14em] text-slate-400">Contact</p>
                <p className="mt-1 text-sm font-semibold text-slate-500">Contact unlocks after acceptance.</p>
            </div>
        );
    }

    return (
        <div className="mt-4 flex flex-wrap items-center justify-between gap-3 rounded-lg border border-emerald-100 bg-emerald-50 px-4 py-3">
            <div>
                <p className="text-xs font-bold uppercase tracking-[0.14em] text-emerald-700">Contact unlocked</p>
                <p className="mt-1 text-sm font-black text-emerald-900">{contact.name} · {contact.phone}</p>
            </div>
            <a href={contact.whatsapp_url} target="_blank" rel="noreferrer" className="rounded-lg bg-emerald-700 px-4 py-2 text-xs font-black text-white">WhatsApp</a>
        </div>
    );
}

// Renders one review left by this traveler.
function ReviewCard({ review }: { review: ReviewSummary }) {
    return (
        <article className="rounded-xl border border-slate-200 bg-slate-50 p-4">
            <div className="flex items-start justify-between gap-3">
                <div className="min-w-0">
                    <p className="font-black text-slate-950">{review.ride?.route ?? 'Completed ride'}</p>
                    <p className="mt-1 text-sm font-semibold text-slate-500">{review.created_label ?? 'recently'}</p>
                </div>
                <span className="shrink-0 rounded-full bg-amber-50 px-3 py-1 text-xs font-black text-amber-700">{review.rating}/5</span>
            </div>
            <p className="mt-3 text-sm leading-6 text-slate-600">{review.comment || 'No written comment.'}</p>
        </article>
    );
}

// Shows an empty state on the traveler profile.
function EmptyState({ title, message }: { title: string; message: string }) {
    return (
        <div className="mt-5 rounded-xl border border-dashed border-slate-200 bg-slate-50 px-5 py-10 text-center">
            <h3 className="text-base font-black text-slate-950">{title}</h3>
            <p className="mx-auto mt-2 max-w-sm text-sm leading-6 text-slate-500">{message}</p>
        </div>
    );
}
