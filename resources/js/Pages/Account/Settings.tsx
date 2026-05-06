import { Link, useForm } from '@inertiajs/react';
import { FormEvent } from 'react';
import { Layout } from '../../components/Layout';
import { ErrorText } from '../../components/ui';
import { path } from '../../routes';
import { UserSummary } from '../../types';

export default function Settings({ user }: { user: UserSummary }) {
    const profileForm = useForm({
        first_name: user.first_name,
        last_name: user.last_name,
        email: user.email,
        phone: user.phone ?? '',
    });
    const passwordForm = useForm({
        current_password: '',
        password: '',
        password_confirmation: '',
    });

    const submitProfile = (event: FormEvent) => {
        event.preventDefault();
        profileForm.patch(path('account.settings.profile.update'));
    };
    const submitPassword = (event: FormEvent) => {
        event.preventDefault();
        passwordForm.patch(path('account.settings.password.update'));
    };

    return (
        <Layout title="Account Settings">
            <section className="py-8 sm:py-12">
                <div className="shell page-enter">
                    <div className="mx-auto max-w-5xl space-y-8">
                        <div className="surface p-8 sm:p-10">
                            <div className="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                                <div>
                                    <p className="text-sm font-semibold uppercase tracking-[0.18em] text-brand-600">Settings</p>
                                    <h1 className="mt-2 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">Account preferences</h1>
                                    <p className="mt-3 max-w-2xl text-slate-500">Keep your profile, password, and identity details up to date.</p>
                                </div>
                                <Link href={path(user.dashboard_route)} className="brand-button-secondary">My account</Link>
                            </div>
                        </div>

                        <div className="grid gap-8 lg:grid-cols-[minmax(0,1fr)_340px]">
                            <div className="space-y-8">
                                <Panel title="Profile details">
                                    <form onSubmit={submitProfile} className="mt-6 grid gap-5 sm:grid-cols-2">
                                        <Field label="First name" value={profileForm.data.first_name} onChange={(value) => profileForm.setData('first_name', value)} error={profileForm.errors.first_name} />
                                        <Field label="Last name" value={profileForm.data.last_name} onChange={(value) => profileForm.setData('last_name', value)} error={profileForm.errors.last_name} />
                                        <Field label="Email" type="email" value={profileForm.data.email} onChange={(value) => profileForm.setData('email', value)} error={profileForm.errors.email} />
                                        <Field label="Phone" type="tel" value={profileForm.data.phone} onChange={(value) => profileForm.setData('phone', value)} error={profileForm.errors.phone} />
                                        <div className="sm:col-span-2"><button type="submit" className="brand-button rounded-[1.25rem]">Save profile</button></div>
                                    </form>
                                </Panel>

                                <Panel title="Password">
                                    <form onSubmit={submitPassword} className="mt-6 grid gap-5">
                                        <Field label="Current password" type="password" value={passwordForm.data.current_password} onChange={(value) => passwordForm.setData('current_password', value)} error={passwordForm.errors.current_password} />
                                        <div className="grid gap-5 sm:grid-cols-2">
                                            <Field label="New password" type="password" value={passwordForm.data.password} onChange={(value) => passwordForm.setData('password', value)} error={passwordForm.errors.password} />
                                            <Field label="Confirm password" type="password" value={passwordForm.data.password_confirmation} onChange={(value) => passwordForm.setData('password_confirmation', value)} />
                                        </div>
                                        <div><button type="submit" className="brand-button rounded-[1.25rem]">Update password</button></div>
                                    </form>
                                </Panel>
                            </div>

                            {user.role !== 'admin' && (
                                <aside className="space-y-8">
                                    <Panel title="Identity">
                                        {user.role === 'driver' ? (
                                            <div className="mt-6 space-y-4 text-sm text-slate-600">
                                                <Info label="Driver profile" value={user.driver_profile?.cin_verified ? 'Identity verified' : 'Identity verification pending'} />
                                                <Info label="CIN number" value={user.driver_profile?.cin_number ?? 'Not provided'} />
                                            </div>
                                        ) : (
                                            <>
                                                <p className="mt-4 text-sm leading-6 text-slate-500">Add your identity and first vehicle to publish rides as a driver.</p>
                                                <Link href={path('drivers.onboarding.create')} className="brand-button mt-6 w-full rounded-[1.25rem]">Become a driver</Link>
                                            </>
                                        )}
                                    </Panel>
                                </aside>
                            )}
                        </div>
                    </div>
                </div>
            </section>
        </Layout>
    );
}

function Panel({ title, children }: { title: string; children: React.ReactNode }) {
    return (
        <div className="rounded-[2.5rem] border border-slate-100 bg-white p-8 shadow-sm">
            <h2 className="text-2xl font-black text-slate-950">{title}</h2>
            {children}
        </div>
    );
}

function Field({ label, value, onChange, error, type = 'text' }: { label: string; value: string; onChange: (value: string) => void; error?: string; type?: string }) {
    return (
        <label className="space-y-2">
            <span className="text-sm font-semibold text-slate-700">{label}</span>
            <input type={type} value={value} onChange={(event) => onChange(event.target.value)} className="w-full rounded-[1.25rem] border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-700 outline-none" />
            <ErrorText message={error} />
        </label>
    );
}

function Info({ label, value }: { label: string; value: string }) {
    return (
        <div className="rounded-2xl bg-slate-50 px-5 py-4">
            <p className="font-semibold text-slate-950">{label}</p>
            <p className="mt-1">{value}</p>
        </div>
    );
}
