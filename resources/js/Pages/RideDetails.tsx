import { useForm } from '@inertiajs/react';
import { FormEvent } from 'react';
import { Layout } from '../components/Layout';
import { StatusChip } from '../components/ui';
import { path } from '../routes';
import { Ride } from '../types';

export default function RideDetails({ ride }: { ride: Ride }) {
    const maxSeats = Math.min(4, ride.available_seats);
    const form = useForm({ seats: '1' });
    const submit = (event: FormEvent) => {
        event.preventDefault();
        form.post(path('rides.book', ride.id));
    };

    return (
        <Layout title={`${ride.departure_city?.name} to ${ride.arrival_city?.name}`}>
            <section className="py-8 sm:py-12">
                <div className="shell page-enter">
                    <div className="grid gap-8 xl:grid-cols-[minmax(0,1fr)_360px]">
                        <div className="space-y-8">
                            <div className="relative overflow-hidden rounded-[3.5rem] border border-slate-100 bg-white p-8 shadow-sm sm:p-12 lg:p-16">
                                <div className="relative z-10 flex flex-col gap-6 sm:flex-row sm:items-start sm:justify-between">
                                    <div>
                                        <div className="mb-6 inline-flex items-center gap-2 rounded-full bg-brand-100 px-4 py-2 text-[12px] font-black uppercase tracking-widest text-brand-600">Ride details</div>
                                        <h1 className="break-words text-[3rem] font-black leading-[0.95] tracking-tight text-slate-900 sm:text-[4.5rem]">
                                            <span className="font-serif italic text-brand-500">{ride.departure_city?.name}</span> &rarr; {ride.arrival_city?.name}
                                        </h1>
                                        <p className="mt-6 text-xl text-slate-500">{ride.departure_full_label} at <span className="font-bold text-slate-900">{ride.departure_time_label}</span></p>
                                    </div>
                                    <div className="origin-top-right scale-110"><StatusChip status={ride.status} /></div>
                                </div>

                                <div className="relative z-10 mt-12 grid gap-6 md:grid-cols-2">
                                    <InfoPanel title="Route">
                                        <div>
                                            <p className="mb-1 text-sm font-bold text-slate-500">Departure</p>
                                            <p className="text-2xl font-black text-slate-900">{ride.departure_city?.name}</p>
                                            <p className="mt-1 text-sm text-slate-500">{ride.meeting_point}</p>
                                        </div>
                                        <div className="h-px bg-slate-200" />
                                        <div>
                                            <p className="mb-1 text-sm font-bold text-slate-500">Arrival</p>
                                            <p className="text-2xl font-black text-slate-900">{ride.arrival_city?.name}</p>
                                            <p className="mt-1 text-sm text-slate-500">Direct city-to-city trip</p>
                                        </div>
                                    </InfoPanel>

                                    <div className="rounded-[2rem] border border-slate-100 bg-slate-50 p-8">
                                        <p className="text-[12px] font-black uppercase tracking-widest text-brand-600">Ride setup</p>
                                        <div className="mt-6 grid gap-6 sm:grid-cols-2">
                                            <Detail label="Vehicle" value={`${ride.vehicle?.brand ?? ''} ${ride.vehicle?.model ?? ''}`} />
                                            <Detail label="Seats left" value={String(ride.available_seats)} />
                                            <Detail label="Price per seat" value={ride.price_label} brand />
                                            <Detail label="Notes" value={ride.notes || 'No additional notes'} />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div className="rounded-[3.5rem] border border-slate-100 bg-white p-8 shadow-sm sm:p-12">
                                <div className="flex flex-col gap-4 sm:flex-row sm:items-center">
                                    <div className="flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-br from-brand-500 to-brand-700 text-lg font-bold text-white">{ride.driver?.initials ?? 'BC'}</div>
                                    <div>
                                        <p className="text-sm font-semibold uppercase tracking-[0.18em] text-slate-400">Driver</p>
                                        <h2 className="mt-1 text-2xl font-bold text-slate-950">{ride.driver?.name}</h2>
                                        <p className="mt-1 text-sm text-slate-500">
                                            Rating {ride.driver?.profile?.avg_rating ?? '0.0'} &middot; {ride.driver?.profile?.total_trips ?? 0} trips &middot; {ride.driver?.profile?.cin_verified ? 'ID verified' : 'Verification pending'}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <aside className="space-y-8">
                            <div className="rounded-[2.5rem] border border-slate-100 bg-white p-8 shadow-sm">
                                <p className="text-[12px] font-black uppercase tracking-widest text-brand-600">Booking card</p>
                                <h2 className="mt-4 text-3xl font-black tracking-tight text-slate-900">Request a seat</h2>
                                <form onSubmit={submit} className="mt-6 space-y-4">
                                    <label className="block">
                                        <span className="text-sm font-medium text-slate-600">Passengers</span>
                                        <select disabled={! ride.can_request} value={form.data.seats} onChange={(event) => form.setData('seats', event.target.value)} className="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-700 outline-none">
                                            {Array.from({ length: Math.max(maxSeats, 1) }).map((_, index) => {
                                                const seat = index + 1;
                                                return <option key={seat} value={seat}>{seat} seat{seat > 1 ? 's' : ''}</option>;
                                            })}
                                        </select>
                                        {form.errors.seats && <p className="mt-2 text-sm font-medium text-red-600">{form.errors.seats}</p>}
                                    </label>
                                    {ride.can_request ? (
                                        <button type="submit" disabled={form.processing} className="brand-button w-full justify-center rounded-[1.25rem]">Request this ride</button>
                                    ) : (
                                        <button type="button" disabled className="inline-flex w-full items-center justify-center rounded-[1.25rem] bg-slate-200 px-5 py-3 font-semibold text-slate-500">Ride is not available</button>
                                    )}
                                </form>
                            </div>
                        </aside>
                    </div>
                </div>
            </section>
        </Layout>
    );
}

function InfoPanel({ title, children }: { title: string; children: React.ReactNode }) {
    return (
        <div className="rounded-[2rem] border border-slate-100 bg-slate-50 p-8">
            <p className="text-[12px] font-black uppercase tracking-widest text-brand-600">{title}</p>
            <div className="mt-6 space-y-6">{children}</div>
        </div>
    );
}

function Detail({ label, value, brand = false }: { label: string; value: string; brand?: boolean }) {
    return (
        <div>
            <p className="mb-1 text-sm font-bold text-slate-500">{label}</p>
            <p className={`text-lg font-black ${brand ? 'text-brand-600' : 'text-slate-900'}`}>{value}</p>
        </div>
    );
}
