import { Head, Link, useForm, usePage } from '@inertiajs/react';
import { PropsWithChildren, useEffect, useState } from 'react';
import { asset, path } from '../routes';
import { SharedProps } from '../types';

type LayoutProps = PropsWithChildren<{
    title?: string;
    showHeader?: boolean;
    showFooter?: boolean;
}>;

export function Layout({ children, title, showHeader = true, showFooter = true }: LayoutProps) {
    const { flash } = usePage<SharedProps>().props;
    const [visible, setVisible] = useState(Boolean(flash.status));

    useEffect(() => {
        setVisible(Boolean(flash.status));

        if (! flash.status) {
            return;
        }

        const timer = window.setTimeout(() => setVisible(false), 3500);

        return () => window.clearTimeout(timer);
    }, [flash.status]);

    return (
        <>
            <Head title={title ? `${title} | BlassaCar` : 'BlassaCar'} />
            <div className="min-h-screen overflow-x-hidden">
                {showHeader && <Header />}
                {flash.status && visible && (
                    <div className="shell pt-6 flash-message">
                        <div className="rounded-[1.5rem] border border-brand-200 bg-brand-50 px-5 py-4 text-sm font-medium text-brand-800">
                            {flash.status}
                        </div>
                    </div>
                )}
                {children}
                {showFooter && <Footer />}
            </div>
        </>
    );
}

function Header() {
    const { auth } = usePage<SharedProps>().props;
    const logout = useForm({});
    const currentUrl = usePage().url.split('?')[0];
    const primaryLinks = [
        { label: 'Home', url: path('home') },
        { label: 'Search rides', url: path('rides.search') },
        { label: 'Publish a ride', url: path('rides.publish') },
        { label: 'How it works', url: `${path('home')}#how-it-works` },
    ];

    const submitLogout = () => logout.post(path('logout'));

    return (
        <header className="w-full z-[100] px-4 py-6 sm:px-6 lg:px-8">
            <nav className="mx-auto w-full max-w-[1800px]">
                <div className="flex items-center justify-between">
                    <div className="flex-shrink-0">
                        <Link href={path('home')} className="flex items-center gap-3 group" aria-label="BlasaCar home">
                            <img src={asset('assets/logoBlasaCar.png')} alt="BlasaCar" className="h-10 w-auto" />
                            <span className="text-2xl font-black tracking-tight text-slate-950">
                                Blasa<span className="text-brand-500">Car</span>
                            </span>
                        </Link>
                    </div>

                    <div className="hidden items-center justify-center rounded-full bg-slate-100 p-1.5 shadow-sm lg:flex">
                        {primaryLinks.map((link) => {
                            const active = currentUrl === link.url;
                            return (
                                <Link
                                    key={link.label}
                                    href={link.url}
                                    className={`rounded-full px-6 py-2.5 text-[14px] font-bold tracking-tight transition-all duration-300 ${active ? 'bg-white text-slate-950 shadow-md' : 'text-slate-500 hover:text-slate-900'}`}
                                >
                                    {link.label}
                                </Link>
                            );
                        })}
                    </div>

                    <div className="hidden items-center gap-6 lg:flex">
                        {! auth.user && (
                            <>
                                <Link href={path('login')} className="text-[14px] font-bold text-slate-500 transition hover:text-slate-950">
                                    Log in
                                </Link>
                                <Link href={path('signup')} className="inline-flex h-12 items-center justify-center rounded-full bg-slate-950 px-8 text-[14px] font-black text-white shadow-xl transition hover:text-brand-500">
                                    Sign up
                                </Link>
                            </>
                        )}

                        {auth.user && (
                            <details className="group relative">
                                <summary aria-label="Open account menu" className="inline-flex h-12 w-12 cursor-pointer list-none items-center justify-center rounded-full bg-slate-950 text-white shadow-xl transition [&::-webkit-details-marker]:hidden">
                                    <svg className="h-5 w-5 transition-colors group-hover:text-brand-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.25" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                                        <path d="M20 21a8 8 0 0 0-16 0" />
                                        <circle cx="12" cy="7" r="4" />
                                    </svg>
                                </summary>
                                <div className="absolute right-0 top-14 z-[120] w-64 overflow-hidden rounded-[1.5rem] border border-slate-200 bg-white p-2 shadow-2xl">
                                    <div className="px-4 py-3">
                                        <p className="text-sm font-black text-slate-950">{auth.user.name}</p>
                                        <p className="mt-1 truncate text-xs font-medium text-slate-500">{auth.user.email}</p>
                                    </div>
                                    <div className="border-t border-slate-100 py-2">
                                        <MenuLink href={path(auth.user.dashboard_route)} label="My account" icon="dashboard" />
                                        <MenuLink href={path('account.settings.edit')} label="Settings" icon="settings" />
                                    </div>
                                    <div className="border-t border-slate-100 pt-2">
                                        <button type="button" onClick={submitLogout} className="flex w-full items-center gap-3 rounded-2xl px-4 py-3 text-left text-sm font-bold text-rose-600 transition hover:bg-rose-50">
                                            <svg className="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" /><path d="m16 17 5-5-5-5" /><path d="M21 12H9" /></svg>
                                            Log out
                                        </button>
                                    </div>
                                </div>
                            </details>
                        )}
                    </div>

                    <details className="group lg:hidden">
                        <summary className="flex h-11 w-11 cursor-pointer list-none items-center justify-center rounded-full border border-slate-200 bg-slate-100 text-slate-900 [&::-webkit-details-marker]:hidden">
                            <svg className="h-6 w-6 group-open:hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5">
                                <line x1="3" x2="21" y1="6" y2="6" />
                                <line x1="3" x2="21" y1="12" y2="12" />
                                <line x1="3" x2="21" y1="18" y2="18" />
                            </svg>
                            <svg className="hidden h-6 w-6 group-open:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5">
                                <path d="M18 6 6 18" />
                                <path d="m6 6 12 12" />
                            </svg>
                        </summary>
                        <div className="fixed inset-x-4 top-24 z-[101] rounded-[2.5rem] border border-white/10 bg-slate-950 p-8 shadow-2xl backdrop-blur-3xl">
                            <div className="space-y-2">
                                {primaryLinks.map((link) => (
                                    <Link key={link.label} href={link.url} className="block rounded-2xl px-6 py-4 text-[16px] font-bold text-white/80 transition hover:bg-white/5 hover:text-white">
                                        {link.label}
                                    </Link>
                                ))}
                            </div>
                            <div className="mt-8 grid gap-4 border-t border-white/10 pt-8">
                                {! auth.user && (
                                    <>
                                        <Link href={path('login')} className="flex h-14 items-center justify-center rounded-2xl border border-white/20 text-[16px] font-bold text-white">Log in</Link>
                                        <Link href={path('signup')} className="flex h-14 items-center justify-center rounded-2xl bg-brand-500 text-[16px] font-bold text-white">Sign up</Link>
                                    </>
                                )}
                                {auth.user && (
                                    <>
                                        <Link href={path(auth.user.dashboard_route)} className="flex h-14 items-center justify-center rounded-2xl bg-brand-500 text-[16px] font-bold text-white">My account</Link>
                                        <Link href={path('account.settings.edit')} className="flex h-14 items-center justify-center rounded-2xl border border-white/20 text-[16px] font-bold text-white">Settings</Link>
                                        <button type="button" onClick={submitLogout} className="flex h-14 w-full items-center justify-center rounded-2xl border border-rose-300/30 text-[16px] font-bold text-rose-100">Log out</button>
                                    </>
                                )}
                            </div>
                        </div>
                    </details>
                </div>
            </nav>
        </header>
    );
}

function MenuLink({ href, label, icon }: { href: string; label: string; icon: 'dashboard' | 'settings' }) {
    return (
        <Link href={href} className="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50 hover:text-brand-700">
            {icon === 'dashboard' ? (
                <svg className="h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><rect x="3" y="3" width="7" height="9" /><rect x="14" y="3" width="7" height="5" /><rect x="14" y="12" width="7" height="9" /><rect x="3" y="16" width="7" height="5" /></svg>
            ) : (
                <svg className="h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><circle cx="12" cy="12" r="3" /><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.9-.3 1.7 1.7 0 0 0-1 1.6V21a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-1-1.6 1.7 1.7 0 0 0-1.9.3l-.1.1A2 2 0 1 1 4.2 17l.1-.1A1.7 1.7 0 0 0 4.6 15a1.7 1.7 0 0 0-1.6-1H3a2 2 0 1 1 0-4h.1a1.7 1.7 0 0 0 1.6-1 1.7 1.7 0 0 0-.3-1.9l-.1-.1A2 2 0 1 1 7 4.2l.1.1A1.7 1.7 0 0 0 9 4.6 1.7 1.7 0 0 0 10 3V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.6 1.7 1.7 0 0 0 1.9-.3l.1-.1A2 2 0 1 1 19.8 7l-.1.1a1.7 1.7 0 0 0-.3 1.9 1.7 1.7 0 0 0 1.6 1H21a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1Z" /></svg>
            )}
            {label}
        </Link>
    );
}

function Footer() {
    const { auth } = usePage<SharedProps>().props;
    const logout = useForm({});

    return (
        <footer className="mt-20 border-t border-slate-200 bg-slate-100/70">
            <div className="shell py-14">
                <div className="grid gap-10 lg:grid-cols-[1.5fr_repeat(2,1fr)]">
                    <div>
                        <Link href={path('home')} className="inline-flex items-center gap-2">
                            <img src={asset('assets/logoBlasaCar.png')} alt="BlassaCar logo" className="h-10 w-auto object-contain" />
                            <span className="text-2xl font-black tracking-tight text-slate-950">Blasa<span className="text-brand-500">Car</span></span>
                        </Link>
                        <p className="mt-6 max-w-md text-lg leading-8 text-slate-600">
                            A modern Moroccan carpooling interface focused on trust, clarity, and affordable intercity travel.
                        </p>
                    </div>
                    <FooterColumn title="Explore" links={[
                        ['Home', path('home')],
                        ['Search rides', path('rides.search')],
                        ['Publish a ride', path('rides.publish')],
                    ]} />
                    <div>
                        <h3 className="text-sm font-semibold uppercase tracking-[0.18em] text-slate-400">Account</h3>
                        <div className="mt-4 space-y-3 text-sm text-slate-600">
                            {! auth.user && (
                                <>
                                    <Link href={path('login')} className="block transition hover:text-brand-700">Log in</Link>
                                    <Link href={path('signup')} className="block transition hover:text-brand-700">Sign up</Link>
                                </>
                            )}
                            {auth.user && (
                                <>
                                    <Link href={path(auth.user.dashboard_route)} className="block transition hover:text-brand-700">My account</Link>
                                    <button type="button" onClick={() => logout.post(path('logout'))} className="block transition hover:text-brand-700">Log out</button>
                                </>
                            )}
                        </div>
                    </div>
                </div>
                <div className="mt-12 border-t border-slate-200 pt-6 text-sm text-slate-500">
                    &copy; 2026 BlassaCar. All rights reserved.
                </div>
            </div>
        </footer>
    );
}

function FooterColumn({ title, links }: { title: string; links: Array<[string, string]> }) {
    return (
        <div>
            <h3 className="text-sm font-semibold uppercase tracking-[0.18em] text-slate-400">{title}</h3>
            <div className="mt-4 space-y-3 text-sm text-slate-600">
                {links.map(([label, href]) => (
                    <Link key={label} href={href} className="block transition hover:text-brand-700">{label}</Link>
                ))}
            </div>
        </div>
    );
}
