import { Head, Link, useForm, usePage } from '@inertiajs/react';
import { PropsWithChildren, ReactNode } from 'react';
import { asset, path } from '../routes';
import { SharedProps } from '../types';

type MobileShellProps = PropsWithChildren<{
    title: string;
    active?: 'home' | 'search' | 'trips' | 'account';
    showNav?: boolean;
    rightAction?: ReactNode;
}>;

type IconProps = {
    className?: string;
};

export function MobileShell({ title, active, showNav = true, rightAction, children }: MobileShellProps) {
    const { auth, flash } = usePage<SharedProps>().props;

    return (
        <>
            <Head title={`${title} | BlasaCar`} />
            <div className="mobile-app min-h-screen bg-[#f7fafc] text-slate-950">
                <div className="mobile-screen mx-auto min-h-screen w-full max-w-[480px] bg-[#f7fafc] pb-28 shadow-[0_0_70px_-45px_rgba(15,23,42,0.45)]">
                    <header className="sticky top-0 z-40 border-b border-slate-200/70 bg-[#f7fafc]/95 px-5 pb-3 pt-[max(1rem,env(safe-area-inset-top))] backdrop-blur-xl">
                        <div className="flex items-center justify-between gap-3">
                            <Link href={path('mobile.home')} className="flex min-w-0 items-center gap-2" aria-label="BlasaCar mobile home">
                                <img src={asset('assets/logoBlasaCar.png')} alt="BlasaCar" className="h-9 w-auto shrink-0" />
                                <span className="truncate text-xl font-black tracking-tight">Blasa<span className="text-brand-500">Car</span></span>
                            </Link>
                            {rightAction ?? (
                                <Link href={path('mobile.account')} className="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-slate-950 text-sm font-black text-white shadow-lg" aria-label="Open account">
                                    {auth.user?.first_name?.slice(0, 1).toUpperCase() ?? <IconUser className="h-5 w-5" />}
                                </Link>
                            )}
                        </div>
                    </header>

                    {flash.status && (
                        <div className="px-5 pt-4">
                            <div className="rounded-lg border border-brand-200 bg-brand-50 px-4 py-3 text-sm font-semibold text-brand-800">
                                {flash.status}
                            </div>
                        </div>
                    )}

                    <main>{children}</main>

                    {showNav && <MobileBottomNav active={active} />}
                </div>
            </div>
        </>
    );
}

function MobileBottomNav({ active }: { active?: MobileShellProps['active'] }) {
    const tabs = [
        { key: 'home', label: 'Home', href: path('mobile.home'), icon: <IconHome /> },
        { key: 'search', label: 'Search', href: path('mobile.search'), icon: <IconSearch /> },
        { key: 'trips', label: 'Trips', href: path('mobile.trips'), icon: <IconTicket /> },
        { key: 'account', label: 'Account', href: path('mobile.account'), icon: <IconUser /> },
    ] as const;

    return (
        <nav className="fixed inset-x-0 bottom-0 z-50 mx-auto max-w-[480px] border-t border-slate-200 bg-white/95 px-3 pb-[max(0.75rem,env(safe-area-inset-bottom))] pt-2 shadow-[0_-24px_60px_-35px_rgba(15,23,42,0.45)] backdrop-blur-xl" aria-label="Mobile app navigation">
            <div className="grid grid-cols-4 gap-1">
                {tabs.map((tab) => {
                    const isActive = active === tab.key;

                    return (
                        <Link
                            key={tab.key}
                            href={tab.href}
                            className={`flex min-h-14 flex-col items-center justify-center rounded-lg text-[11px] font-black transition ${isActive ? 'bg-slate-950 text-white' : 'text-slate-500 active:bg-slate-100'}`}
                        >
                            <span className="mb-1">{tab.icon}</span>
                            {tab.label}
                        </Link>
                    );
                })}
            </div>
        </nav>
    );
}

export function AuthPrompt({ title, message }: { title: string; message: string }) {
    const currentUrl = usePage().url;
    const redirectTo = currentUrl.startsWith('/mobile') ? currentUrl : path('mobile.home');
    const loginHref = `${path('mobile.login')}?redirect_to=${encodeURIComponent(redirectTo)}`;
    const signupHref = `${path('mobile.signup')}?redirect_to=${encodeURIComponent(redirectTo)}`;

    return (
        <section className="px-5 py-6">
            <div className="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <div className="flex h-12 w-12 items-center justify-center rounded-lg bg-brand-50 text-brand-700">
                    <IconUser />
                </div>
                <h1 className="mt-5 text-2xl font-black tracking-tight text-slate-950">{title}</h1>
                <p className="mt-2 text-sm font-medium leading-6 text-slate-500">{message}</p>
                <div className="mt-5 grid grid-cols-2 gap-3">
                    <Link href={loginHref} className="flex h-12 items-center justify-center rounded-lg bg-slate-950 text-sm font-black text-white">Log in</Link>
                    <Link href={signupHref} className="flex h-12 items-center justify-center rounded-lg border border-slate-200 bg-white text-sm font-black text-slate-800">Sign up</Link>
                </div>
            </div>
        </section>
    );
}

export function LogoutButton() {
    const logout = useForm({ redirect_to: path('mobile.home') });

    return (
        <button
            type="button"
            onClick={() => logout.post(path('logout'))}
            disabled={logout.processing}
            className="flex h-12 w-full items-center justify-center rounded-lg border border-rose-200 bg-rose-50 text-sm font-black text-rose-700 disabled:opacity-60"
        >
            Log out
        </button>
    );
}

export function IconHome({ className = 'h-5 w-5' }: IconProps) {
    return <svg className={className} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2" strokeLinecap="round" strokeLinejoin="round"><path d="m3 11 9-8 9 8" /><path d="M5 10v10h14V10" /><path d="M9 20v-6h6v6" /></svg>;
}

export function IconSearch({ className = 'h-5 w-5' }: IconProps) {
    return <svg className={className} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2" strokeLinecap="round"><circle cx="11" cy="11" r="8" /><path d="m21 21-4.3-4.3" /></svg>;
}

export function IconTicket({ className = 'h-5 w-5' }: IconProps) {
    return <svg className={className} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2" strokeLinecap="round" strokeLinejoin="round"><path d="M2 9a3 3 0 0 0 0 6v3a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-3a3 3 0 0 0 0-6V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z" /><path d="M13 5v14" /></svg>;
}

export function IconUser({ className = 'h-5 w-5' }: IconProps) {
    return <svg className={className} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2" strokeLinecap="round"><path d="M20 21a8 8 0 0 0-16 0" /><circle cx="12" cy="7" r="4" /></svg>;
}

export function IconPin({ className = 'h-4 w-4' }: IconProps) {
    return <svg className={className} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2" strokeLinecap="round"><path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 0 1 16 0Z" /><circle cx="12" cy="10" r="3" /></svg>;
}

export function IconCalendar({ className = 'h-4 w-4' }: IconProps) {
    return <svg className={className} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2" strokeLinecap="round"><rect x="3" y="4" width="18" height="18" rx="2" /><path d="M16 2v4M8 2v4M3 10h18" /></svg>;
}

export function IconClock({ className = 'h-4 w-4' }: IconProps) {
    return <svg className={className} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2" strokeLinecap="round"><circle cx="12" cy="12" r="9" /><path d="M12 7v5l3 2" /></svg>;
}

export function IconArrowRight({ className = 'h-4 w-4' }: IconProps) {
    return <svg className={className} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2" strokeLinecap="round"><path d="M5 12h14M13 5l7 7-7 7" /></svg>;
}

export function IconCar({ className = 'h-5 w-5' }: IconProps) {
    return <svg className={className} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2" strokeLinecap="round"><path d="M19 17h2l-1.4-4.2A3 3 0 0 0 16.8 11H7.2a3 3 0 0 0-2.8 1.8L3 17h2" /><circle cx="7" cy="17" r="2" /><circle cx="17" cy="17" r="2" /><path d="M8 11l1.4-4h5.2L16 11" /></svg>;
}
