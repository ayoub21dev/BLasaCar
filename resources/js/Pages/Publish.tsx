import { Link, useForm, usePage } from '@inertiajs/react';
import { FormEvent } from 'react';
import { CityCombobox } from '../components/CityCombobox';
import { Layout } from '../components/Layout';
import { ErrorText } from '../components/ui';
import { path } from '../routes';
import { City, SharedProps, Vehicle } from '../types';

type PublishProps = {
    cities: City[];
    canPublishRide: boolean;
    verificationPending: boolean;
    vehicles: Vehicle[];
};

export default function Publish({ cities, canPublishRide, verificationPending, vehicles }: PublishProps) {
    const { auth } = usePage<SharedProps>().props;
    const form = useForm({
        vehicle_id: '',
        departure_city_id: '',
        arrival_city_id: '',
        departure_date: '',
        departure_time: '',
        seats_offered: '1',
        price_per_seat: '',
        meeting_point: '',
        notes: '',
    });

    const submit = (event: FormEvent) => {
        event.preventDefault();
        form.post(path('rides.publish.store'));
    };

    return (
        <Layout title="Publish a Ride">
            <section className="py-8 sm:py-12">
                <div className="shell page-enter">
                    <div className="mx-auto max-w-6xl">
                        <div className="overflow-hidden rounded-[3.5rem] border border-slate-100 bg-white shadow-sm">
                            <div className="relative px-8 py-12 sm:px-16 sm:py-16">
                                <div className="relative z-10">
                                    <h1 className="text-[3rem] font-black leading-[0.95] tracking-tight text-slate-900 sm:text-[4.5rem]">
                                        Publish a <span className="font-serif italic text-brand-500">ride</span>.
                                    </h1>
                                    <p className="mt-6 max-w-xl text-lg text-slate-500">Share your journey, save on travel costs, and meet great people along the way.</p>
                                </div>
                            </div>

                            {verificationPending ? (
                                <div className="p-5 sm:p-8">
                                    <div className="rounded-[2rem] border border-amber-200 bg-amber-50 p-8 text-amber-900">
                                        <h2 className="text-2xl font-black text-slate-950">Verification pending</h2>
                                        <p className="mt-3 max-w-2xl text-sm font-semibold leading-6 text-amber-800">Your driver profile is waiting for CIN verification. You can publish rides after an admin verifies your identity.</p>
                                        <Link href={path('dashboards.driver')} className="mt-6 inline-flex rounded-full bg-amber-600 px-5 py-3 text-sm font-black text-white transition hover:bg-amber-700">Back to dashboard</Link>
                                    </div>
                                </div>
                            ) : (
                                <form onSubmit={submit} className="grid gap-6 p-5 sm:p-8 lg:grid-cols-2">
                                    {auth.user?.role === 'driver' && (
                                        <label className="space-y-2 lg:col-span-2">
                                            <span className="text-sm font-semibold text-slate-700">Vehicle</span>
                                            <select value={form.data.vehicle_id} onChange={(event) => form.setData('vehicle_id', event.target.value)} className="w-full rounded-[1.25rem] border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-700 outline-none">
                                                <option value="">Select vehicle</option>
                                                {vehicles.map((vehicle) => <option key={vehicle.id} value={vehicle.id}>{vehicle.brand} {vehicle.model}</option>)}
                                            </select>
                                            <ErrorText message={form.errors.vehicle_id} />
                                        </label>
                                    )}
                                    <div className="space-y-2">
                                        <span className="text-sm font-semibold text-slate-700">From</span>
                                        <CityCombobox cities={cities} value={form.data.departure_city_id} onChange={(value) => form.setData('departure_city_id', value)} inputClassName="w-full rounded-[1.25rem] border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-700 outline-none" />
                                        <ErrorText message={form.errors.departure_city_id} />
                                    </div>
                                    <div className="space-y-2">
                                        <span className="text-sm font-semibold text-slate-700">To</span>
                                        <CityCombobox cities={cities} value={form.data.arrival_city_id} onChange={(value) => form.setData('arrival_city_id', value)} inputClassName="w-full rounded-[1.25rem] border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-700 outline-none" />
                                        <ErrorText message={form.errors.arrival_city_id} />
                                    </div>
                                    <TextField label="Date" type="date" value={form.data.departure_date} onChange={(value) => form.setData('departure_date', value)} error={form.errors.departure_date} />
                                    <TextField label="Departure time" type="time" value={form.data.departure_time} onChange={(value) => form.setData('departure_time', value)} error={form.errors.departure_time} />
                                    <label className="space-y-2">
                                        <span className="text-sm font-semibold text-slate-700">Seats offered</span>
                                        <select value={form.data.seats_offered} onChange={(event) => form.setData('seats_offered', event.target.value)} className="w-full rounded-[1.25rem] border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-700 outline-none">
                                            {[1, 2, 3, 4].map((seat) => <option key={seat} value={seat}>{seat} seat{seat > 1 ? 's' : ''}</option>)}
                                        </select>
                                        <ErrorText message={form.errors.seats_offered} />
                                    </label>
                                    <TextField label="Price per seat (DH)" type="number" placeholder="70" value={form.data.price_per_seat} onChange={(value) => form.setData('price_per_seat', value)} error={form.errors.price_per_seat} />
                                    <TextField label="Meeting point" placeholder="Casa Voyageurs taxi lane" value={form.data.meeting_point} onChange={(value) => form.setData('meeting_point', value)} error={form.errors.meeting_point} wide />
                                    <label className="space-y-2 lg:col-span-2">
                                        <span className="text-sm font-semibold text-slate-700">Notes</span>
                                        <textarea rows={5} value={form.data.notes} onChange={(event) => form.setData('notes', event.target.value)} placeholder="Luggage policy, flexible pickup, or other details" className="w-full rounded-[1.25rem] border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-700 outline-none" />
                                        <ErrorText message={form.errors.notes} />
                                    </label>
                                    <div className="lg:col-span-2">
                                        {! auth.user ? (
                                            <Link href={path('login')} className="brand-button w-full justify-center rounded-[1.4rem] py-4 text-base">Log in to publish</Link>
                                        ) : canPublishRide && vehicles.length > 0 ? (
                                            <button type="submit" disabled={form.processing} className="brand-button w-full justify-center rounded-[1.4rem] py-4 text-base">Publish my ride</button>
                                        ) : auth.user.role === 'traveler' ? (
                                            <Link href={path('drivers.onboarding.create')} className="brand-button w-full justify-center rounded-[1.4rem] py-4 text-base">Become a driver first</Link>
                                        ) : (
                                            <button type="button" disabled className="inline-flex w-full items-center justify-center rounded-[1.4rem] bg-slate-200 px-5 py-4 text-base font-semibold text-slate-500">Driver account with a vehicle required</button>
                                        )}
                                    </div>
                                </form>
                            )}
                        </div>
                    </div>
                </div>
            </section>
        </Layout>
    );
}

function TextField({ label, value, onChange, error, type = 'text', placeholder, wide = false }: { label: string; value: string; onChange: (value: string) => void; error?: string; type?: string; placeholder?: string; wide?: boolean }) {
    return (
        <label className={`space-y-2 ${wide ? 'lg:col-span-2' : ''}`}>
            <span className="text-sm font-semibold text-slate-700">{label}</span>
            <input type={type} value={value} onChange={(event) => onChange(event.target.value)} placeholder={placeholder} className="w-full rounded-[1.25rem] border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-700 outline-none" />
            <ErrorText message={error} />
        </label>
    );
}
