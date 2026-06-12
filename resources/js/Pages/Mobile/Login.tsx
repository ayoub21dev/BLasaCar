import { Link, useForm, usePage } from '@inertiajs/react';
import { FormEvent } from 'react';
import { IconArrowRight, IconUser, MobileShell } from '../../components/MobileShell';
import { ErrorText } from '../../components/ui';
import { path } from '../../routes';

// Shows the mobile login form and preserves the desired post-login target.
export default function Login() {
    const page = usePage();
    const redirectTo = redirectTarget(page.url, path('mobile.home'));
    const form = useForm({
        email: '',
        password: '',
        remember: true,
        redirect_to: redirectTo,
    });

    // Submits credentials to the shared web login endpoint.
    const submit = (event: FormEvent) => {
        event.preventDefault();
        form.post(path('login.store'));
    };

    return (
        <MobileShell title="Log in" showNav={false} rightAction={<Link href={path('mobile.home')} className="text-sm font-black text-slate-700">Skip</Link>}>
            <section className="px-5 pb-8 pt-6">
                <div className="rounded-lg bg-slate-950 p-5 text-white shadow-xl shadow-slate-900/10">
                    <div className="flex h-12 w-12 items-center justify-center rounded-full bg-white text-slate-950">
                        <IconUser />
                    </div>
                    <h1 className="mt-5 text-3xl font-black tracking-tight">Welcome back</h1>
                    <p className="mt-2 text-sm font-semibold leading-6 text-white/70">Log in to book rides and manage your trips.</p>
                </div>

                <form onSubmit={submit} className="mt-5 space-y-4 rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                    <MobileField label="Email address">
                        <input
                            type="email"
                            value={form.data.email}
                            onChange={(event) => form.setData('email', event.target.value)}
                            placeholder="you@example.com"
                            autoComplete="email"
                            className="h-12 w-full rounded-lg border border-slate-200 bg-white px-4 text-sm font-black text-slate-800 outline-none focus:border-brand-400 focus:ring-4 focus:ring-brand-50"
                        />
                    </MobileField>
                    <ErrorText message={form.errors.email} />

                    <MobileField label="Password">
                        <input
                            type="password"
                            value={form.data.password}
                            onChange={(event) => form.setData('password', event.target.value)}
                            placeholder="Password"
                            autoComplete="current-password"
                            className="h-12 w-full rounded-lg border border-slate-200 bg-white px-4 text-sm font-black text-slate-800 outline-none focus:border-brand-400 focus:ring-4 focus:ring-brand-50"
                        />
                    </MobileField>
                    <ErrorText message={form.errors.password} />

                    <label className="flex items-center gap-2 text-sm font-bold text-slate-600">
                        <input
                            type="checkbox"
                            checked={form.data.remember}
                            onChange={(event) => form.setData('remember', event.target.checked)}
                            className="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-600"
                        />
                        Remember me
                    </label>

                    <button type="submit" disabled={form.processing} className="flex h-12 w-full items-center justify-center gap-2 rounded-lg bg-slate-950 text-sm font-black text-white disabled:opacity-60">
                        Log in
                        <IconArrowRight />
                    </button>
                </form>

                <p className="mt-5 text-center text-sm font-semibold text-slate-500">
                    New to BlasaCar? <Link href={`${path('mobile.signup')}?redirect_to=${encodeURIComponent(redirectTo)}`} className="font-black text-brand-700">Create account</Link>
                </p>
            </section>
        </MobileShell>
    );
}

// Renders one labeled mobile auth field wrapper.
function MobileField({ label, children }: { label: string; children: React.ReactNode }) {
    return (
        <label className="block space-y-1.5">
            <span className="text-[11px] font-black uppercase tracking-wide text-slate-400">{label}</span>
            {children}
        </label>
    );
}

// Reads a safe mobile redirect target from the current URL.
function redirectTarget(pageUrl: string, fallback: string): string {
    const queryString = pageUrl.split('?')[1] ?? '';
    const redirectTo = new URLSearchParams(queryString).get('redirect_to');

    return redirectTo?.startsWith('/mobile') ? redirectTo : fallback;
}
