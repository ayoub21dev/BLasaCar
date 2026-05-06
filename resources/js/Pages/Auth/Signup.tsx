import { Link, useForm } from '@inertiajs/react';
import { FormEvent } from 'react';
import { Layout } from '../../components/Layout';
import { asset, path } from '../../routes';
import { ErrorText } from '../../components/ui';

export default function Signup() {
    const form = useForm({
        full_name: '',
        phone: '',
        email: '',
        password: '',
    });

    const submit = (event: FormEvent) => {
        event.preventDefault();
        form.post(path('signup.store'));
    };

    return (
        <Layout title="Sign Up">
            <section className="py-16">
                <div className="shell page-enter">
                    <div className="mx-auto max-w-md">
                        <div className="surface p-8 sm:p-10">
                            <div className="text-center">
                                <img src={asset('assets/logoBlasaCar.png')} alt="BlassaCar logo" className="mx-auto h-14 w-auto object-contain" />
                                <h1 className="mt-8 text-3xl font-black text-slate-950">Create an account</h1>
                                <p className="mt-3 text-slate-500">Join the community and start saving on your next trip.</p>
                            </div>

                            <form onSubmit={submit} className="mt-8 space-y-4">
                                <Field icon="user" value={form.data.full_name} onChange={(value) => form.setData('full_name', value)} placeholder="Full name" />
                                <ErrorText message={form.errors.full_name} />
                                <Field icon="phone" value={form.data.phone} onChange={(value) => form.setData('phone', value)} placeholder="Phone number" type="tel" />
                                <ErrorText message={form.errors.phone} />
                                <Field icon="email" value={form.data.email} onChange={(value) => form.setData('email', value)} placeholder="Email address" type="email" />
                                <ErrorText message={form.errors.email} />
                                <Field icon="lock" value={form.data.password} onChange={(value) => form.setData('password', value)} placeholder="Create a password" type="password" />
                                <ErrorText message={form.errors.password} />

                                <button type="submit" disabled={form.processing} className="brand-button w-full justify-center rounded-[1.25rem] py-4 text-base">
                                    Sign up
                                </button>
                            </form>

                            <p className="mt-6 text-center text-sm text-slate-500">
                                Already have an account? <Link href={path('login')} className="font-semibold text-brand-700 hover:text-brand-800">Log in</Link>
                            </p>
                        </div>
                    </div>
                </div>
            </section>
        </Layout>
    );
}

function Field({ icon, value, onChange, placeholder, type = 'text' }: { icon: 'user' | 'phone' | 'email' | 'lock'; value: string; onChange: (value: string) => void; placeholder: string; type?: string }) {
    return (
        <div className="input-shell">
            {icon === 'user' && <svg className="h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" /><circle cx="12" cy="7" r="4" /></svg>}
            {icon === 'phone' && <svg className="h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z" /></svg>}
            {icon === 'email' && <svg className="h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><rect width="20" height="16" x="2" y="4" rx="2" /><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" /></svg>}
            {icon === 'lock' && <svg className="h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><rect width="18" height="11" x="3" y="11" rx="2" ry="2" /><path d="M7 11V7a5 5 0 0 1 10 0v4" /></svg>}
            <input type={type} value={value} onChange={(event) => onChange(event.target.value)} placeholder={placeholder} className="w-full bg-transparent text-sm font-medium text-slate-700 outline-none" />
        </div>
    );
}
