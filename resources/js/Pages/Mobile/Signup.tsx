import { Link, useForm, usePage } from '@inertiajs/react';
import { FormEvent } from 'react';
import { IconArrowRight, IconUser, MobileShell } from '../../components/MobileShell';
import { ErrorText } from '../../components/ui';
import { path } from '../../routes';

export default function Signup() {
    const page = usePage();
    const redirectTo = redirectTarget(page.url, path('mobile.home'));
    const form = useForm({
        full_name: '',
        phone: '',
        email: '',
        password: '',
        redirect_to: redirectTo,
    });

    const submit = (event: FormEvent) => {
        event.preventDefault();
        form.post(path('signup.store'));
    };

    return (
        <MobileShell title="Sign up" showNav={false} rightAction={<Link href={path('mobile.home')} className="text-sm font-black text-slate-700">Skip</Link>}>
            <section className="px-5 pb-8 pt-6">
                <div className="rounded-lg bg-slate-950 p-5 text-white shadow-xl shadow-slate-900/10">
                    <div className="flex h-12 w-12 items-center justify-center rounded-full bg-white text-slate-950">
                        <IconUser />
                    </div>
                    <h1 className="mt-5 text-3xl font-black tracking-tight">Create account</h1>
                    <p className="mt-2 text-sm font-semibold leading-6 text-white/70">Join as a traveler and start booking rides.</p>
                </div>

                <form onSubmit={submit} className="mt-5 space-y-4 rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                    <MobileField label="Full name" value={form.data.full_name} onChange={(value) => form.setData('full_name', value)} placeholder="Your name" autoComplete="name" />
                    <ErrorText message={form.errors.full_name} />
                    <MobileField label="Phone number" value={form.data.phone} onChange={(value) => form.setData('phone', value)} placeholder="+212..." type="tel" autoComplete="tel" />
                    <ErrorText message={form.errors.phone} />
                    <MobileField label="Email address" value={form.data.email} onChange={(value) => form.setData('email', value)} placeholder="you@example.com" type="email" autoComplete="email" />
                    <ErrorText message={form.errors.email} />
                    <MobileField label="Password" value={form.data.password} onChange={(value) => form.setData('password', value)} placeholder="At least 8 characters" type="password" autoComplete="new-password" />
                    <ErrorText message={form.errors.password} />

                    <button type="submit" disabled={form.processing} className="flex h-12 w-full items-center justify-center gap-2 rounded-lg bg-slate-950 text-sm font-black text-white disabled:opacity-60">
                        Sign up
                        <IconArrowRight />
                    </button>
                </form>

                <p className="mt-5 text-center text-sm font-semibold text-slate-500">
                    Already have an account? <Link href={`${path('mobile.login')}?redirect_to=${encodeURIComponent(redirectTo)}`} className="font-black text-brand-700">Log in</Link>
                </p>
            </section>
        </MobileShell>
    );
}

function MobileField({ label, value, onChange, placeholder, type = 'text', autoComplete }: { label: string; value: string; onChange: (value: string) => void; placeholder: string; type?: string; autoComplete?: string }) {
    return (
        <label className="block space-y-1.5">
            <span className="text-[11px] font-black uppercase tracking-wide text-slate-400">{label}</span>
            <input
                type={type}
                value={value}
                onChange={(event) => onChange(event.target.value)}
                placeholder={placeholder}
                autoComplete={autoComplete}
                className="h-12 w-full rounded-lg border border-slate-200 bg-white px-4 text-sm font-black text-slate-800 outline-none focus:border-brand-400 focus:ring-4 focus:ring-brand-50"
            />
        </label>
    );
}

function redirectTarget(pageUrl: string, fallback: string): string {
    const queryString = pageUrl.split('?')[1] ?? '';
    const redirectTo = new URLSearchParams(queryString).get('redirect_to');

    return redirectTo?.startsWith('/mobile') ? redirectTo : fallback;
}
