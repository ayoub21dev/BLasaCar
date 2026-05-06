import { useForm } from '@inertiajs/react';
import { FormEvent } from 'react';
import { Layout } from '../../components/Layout';
import { ErrorText } from '../../components/ui';
import { path } from '../../routes';

export default function Onboarding() {
    const form = useForm({
        cin_number: '',
        cin_front_photo: null as File | null,
        cin_back_photo: null as File | null,
        vehicle_brand: '',
        vehicle_model: '',
    });

    const submit = (event: FormEvent) => {
        event.preventDefault();
        form.post(path('drivers.onboarding.store'), { forceFormData: true });
    };

    return (
        <Layout title="Become a Driver">
            <section className="py-8 sm:py-12">
                <div className="shell page-enter">
                    <div className="mx-auto max-w-5xl">
                        <div className="overflow-hidden rounded-[3.5rem] border border-slate-100 bg-white shadow-sm">
                            <div className="px-8 py-12 sm:px-16 sm:py-16">
                                <div className="mb-6 inline-flex items-center gap-2 rounded-full bg-brand-100 px-4 py-2 text-[12px] font-black uppercase tracking-widest text-brand-600">Driver onboarding</div>
                                <h1 className="text-[3rem] font-black leading-[0.95] tracking-tight text-slate-900 sm:text-[4.5rem]">
                                    Become a <span className="font-serif italic text-brand-500">driver</span>.
                                </h1>
                                <p className="mt-6 max-w-2xl text-lg text-slate-500">Your account starts as a traveler. Add your identity and first vehicle to unlock ride publishing.</p>
                            </div>

                            <form onSubmit={submit} className="grid gap-6 p-5 sm:p-8 lg:grid-cols-2">
                                <TextField label="CIN number" value={form.data.cin_number} onChange={(value) => form.setData('cin_number', value)} error={form.errors.cin_number} placeholder="BE123456" />
                                <FileField label="CIN front photo" onChange={(file) => form.setData('cin_front_photo', file)} error={form.errors.cin_front_photo} />
                                <FileField label="CIN back photo" onChange={(file) => form.setData('cin_back_photo', file)} error={form.errors.cin_back_photo} />
                                <TextField label="Vehicle brand" value={form.data.vehicle_brand} onChange={(value) => form.setData('vehicle_brand', value)} error={form.errors.vehicle_brand} placeholder="Dacia" />
                                <TextField label="Vehicle model" value={form.data.vehicle_model} onChange={(value) => form.setData('vehicle_model', value)} error={form.errors.vehicle_model} placeholder="Logan" />
                                <div className="lg:col-span-2">
                                    <button type="submit" disabled={form.processing} className="brand-button w-full justify-center rounded-[1.4rem] py-4 text-base">Create driver profile</button>
                                    <p className="mt-4 text-center text-sm leading-6 text-slate-500">CIN verification starts as pending. Publishing is available after an admin verifies your CIN number and both ID photos.</p>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </Layout>
    );
}

function TextField({ label, value, onChange, error, placeholder }: { label: string; value: string; onChange: (value: string) => void; error?: string; placeholder?: string }) {
    return (
        <label className="space-y-2">
            <span className="text-sm font-semibold text-slate-700">{label}</span>
            <input type="text" value={value} onChange={(event) => onChange(event.target.value)} placeholder={placeholder} className="w-full rounded-[1.25rem] border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-700 outline-none" />
            <ErrorText message={error} />
        </label>
    );
}

function FileField({ label, onChange, error }: { label: string; onChange: (file: File | null) => void; error?: string }) {
    return (
        <label className="space-y-2">
            <span className="text-sm font-semibold text-slate-700">{label}</span>
            <input type="file" accept="image/png,image/jpeg,image/webp" onChange={(event) => onChange(event.target.files?.[0] ?? null)} className="w-full rounded-[1.25rem] border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-700 outline-none file:mr-4 file:rounded-full file:border-0 file:bg-white file:px-4 file:py-2 file:text-sm file:font-bold file:text-brand-700" />
            <ErrorText message={error} />
        </label>
    );
}
