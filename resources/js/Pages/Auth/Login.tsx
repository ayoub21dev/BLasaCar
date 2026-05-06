import { Link, useForm } from '@inertiajs/react';
import { FormEvent } from 'react';
import { Layout } from '../../components/Layout';
import { asset, path } from '../../routes';

export default function Login() {
    const form = useForm({
        email: '',
        password: '',
        remember: false,
    });

    const submit = (event: FormEvent) => {
        event.preventDefault();
        form.post(path('login.store'));
    };

    return (
        <Layout title="Log in">
            <section className="py-16">
                <div className="shell page-enter">
                    <div className="mx-auto max-w-md">
                        <div className="surface p-8 sm:p-10">
                            <div className="text-center">
                                <img src={asset('assets/logoBlasaCar.png')} alt="BlassaCar logo" className="mx-auto h-14 w-auto object-contain" />
                                <h1 className="mt-8 text-3xl font-black text-slate-950">Welcome back</h1>
                                <p className="mt-3 text-slate-500">Log in to manage rides, bookings, and account details.</p>
                            </div>

                            <form onSubmit={submit} className="mt-8 space-y-4">
                                <label className="block">
                                    <span className="sr-only">Email address</span>
                                    <div className="input-shell">
                                        <svg className="h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><rect width="20" height="16" x="2" y="4" rx="2" /><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" /></svg>
                                        <input type="email" value={form.data.email} onChange={(event) => form.setData('email', event.target.value)} placeholder="Email address" className="w-full bg-transparent text-sm font-medium text-slate-700 outline-none" />
                                    </div>
                                    {form.errors.email && <p className="mt-2 text-sm font-medium text-rose-600">{form.errors.email}</p>}
                                </label>

                                <label className="block">
                                    <span className="sr-only">Password</span>
                                    <div className="input-shell">
                                        <svg className="h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><rect width="18" height="11" x="3" y="11" rx="2" ry="2" /><path d="M7 11V7a5 5 0 0 1 10 0v4" /></svg>
                                        <input type="password" value={form.data.password} onChange={(event) => form.setData('password', event.target.value)} placeholder="Password" className="w-full bg-transparent text-sm font-medium text-slate-700 outline-none" />
                                    </div>
                                    {form.errors.password && <p className="mt-2 text-sm font-medium text-rose-600">{form.errors.password}</p>}
                                </label>

                                <div className="flex items-center justify-between text-sm">
                                    <label className="flex items-center gap-2 text-slate-500">
                                        <input type="checkbox" checked={form.data.remember} onChange={(event) => form.setData('remember', event.target.checked)} className="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-600" />
                                        Remember me
                                    </label>
                                </div>

                                <button type="submit" disabled={form.processing} className="brand-button w-full justify-center rounded-[1.25rem] py-4 text-base">
                                    Log in
                                </button>
                            </form>

                            <p className="mt-6 text-center text-sm text-slate-500">
                                Not a member yet? <Link href={path('signup')} className="font-semibold text-brand-700 hover:text-brand-800">Create an account</Link>
                            </p>
                        </div>
                    </div>
                </div>
            </section>
        </Layout>
    );
}
