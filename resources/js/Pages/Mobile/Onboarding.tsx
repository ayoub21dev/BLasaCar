import { Link, router } from '@inertiajs/react';
import { useEffect } from 'react';
import { IconArrowRight, IconCar, IconSearch, IconTicket, MobileShell } from '../../components/MobileShell';
import { asset, path } from '../../routes';

const onboardingKey = 'blasacar_mobile_onboarding_seen';

export default function Onboarding() {
    useEffect(() => {
        if (typeof window !== 'undefined' && window.localStorage.getItem(onboardingKey) === '1') {
            router.visit(path('mobile.home'), { replace: true });
        }
    }, []);

    const start = () => {
        if (typeof window !== 'undefined') {
            window.localStorage.setItem(onboardingKey, '1');
        }

        router.visit(path('mobile.home'));
    };

    return (
        <MobileShell title="Welcome" showNav={false} rightAction={<Link href={path('mobile.login')} className="text-sm font-black text-slate-700">Log in</Link>}>
            <section className="px-5 pb-8 pt-4">
                <div className="relative min-h-[34rem] overflow-hidden rounded-lg bg-slate-950 shadow-2xl">
                    <img src={asset('images/Heropage.png')} alt="Moroccan road trip" className="absolute inset-0 h-full w-full object-cover object-bottom opacity-80" />
                    <div className="absolute inset-0 bg-gradient-to-b from-slate-950/20 via-slate-950/25 to-slate-950/90" />
                    <div className="relative flex min-h-[34rem] flex-col justify-end p-5 text-white">
                        <div className="mb-5 inline-flex w-fit items-center gap-2 rounded-full bg-white/15 px-3 py-2 text-xs font-black backdrop-blur">
                            <IconCar />
                            Morocco carpooling
                        </div>
                        <h1 className="text-4xl font-black leading-[1.02] tracking-tight">
                            Book intercity rides without the desktop noise.
                        </h1>
                        <p className="mt-3 text-sm font-semibold leading-6 text-white/80">
                            Search, book, check trips, and manage your account from one mobile flow.
                        </p>
                    </div>
                </div>

                <div className="mt-5 grid grid-cols-3 gap-2">
                    <OnboardingStep icon={<IconSearch />} label="Search" />
                    <OnboardingStep icon={<IconTicket />} label="Trips" />
                    <OnboardingStep icon={<IconCar />} label="Drivers" />
                </div>

                <button type="button" onClick={start} className="mt-5 flex h-12 w-full items-center justify-center gap-2 rounded-lg bg-brand-500 text-base font-black text-white shadow-xl shadow-brand-500/20 active:scale-[0.99]">
                    Get started
                    <IconArrowRight />
                </button>
                <Link href={path('mobile.home')} className="mt-3 flex h-12 items-center justify-center rounded-lg border border-slate-200 bg-white text-sm font-black text-slate-700">
                    Continue as guest
                </Link>
            </section>
        </MobileShell>
    );
}

function OnboardingStep({ icon, label }: { icon: React.ReactNode; label: string }) {
    return (
        <div className="rounded-lg border border-slate-200 bg-white px-3 py-4 text-center shadow-sm">
            <div className="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-brand-50 text-brand-700">{icon}</div>
            <p className="mt-2 text-xs font-black text-slate-700">{label}</p>
        </div>
    );
}
