import { Link, usePage } from '@inertiajs/react';
import { path } from '../routes';

export function ErrorText({ message }: { message?: string }) {
    if (! message) {
        return null;
    }

    return <p className="text-sm font-medium text-red-600">{message}</p>;
}

export function StatusChip({ status, label }: { status: string; label?: string }) {
    const map: Record<string, string> = {
        scheduled: 'bg-sky-100 text-sky-700',
        pending: 'bg-amber-100 text-amber-700',
        confirmed: 'bg-emerald-100 text-emerald-700',
        rejected: 'bg-rose-100 text-rose-700',
        completed: 'bg-emerald-100 text-emerald-700',
        cancelled: 'bg-rose-100 text-rose-700',
        active: 'bg-emerald-100 text-emerald-700',
        suspended: 'bg-rose-100 text-rose-700',
    };
    const displayLabel = label ?? (status === 'confirmed' ? 'Accepted' : status.replace(/_/g, ' ').replace(/^\w/, (char) => char.toUpperCase()));

    return <span className={`inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold ${map[status] ?? 'bg-slate-100 text-slate-700'}`}>{displayLabel}</span>;
}

export function StatTile({ label, value }: { label: string; value: string | number }) {
    return (
        <div className="stat-tile">
            <p className="text-sm text-slate-500">{label}</p>
            <p className="mt-2 text-3xl font-bold text-slate-950">{value}</p>
        </div>
    );
}

export function DashboardSidebar({ label, items }: { label: string; items: Array<{ label: string; route: string; icon: string }> }) {
    const currentUrl = usePage().url.split('?')[0];

    return (
        <aside className="hidden w-72 shrink-0 md:block">
            <div className="sticky top-24 space-y-6">
                <div className="surface-soft p-6">
                    <p className="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{label}</p>
                    <nav className="mt-5 space-y-2">
                        {items.map((item) => {
                            const href = path(item.route);
                            const active = currentUrl === href;
                            return (
                                <Link key={item.route} href={href} className={`flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium transition ${active ? 'bg-brand-50 text-brand-700' : 'text-slate-600 hover:bg-slate-50 hover:text-brand-700'}`}>
                                    <span dangerouslySetInnerHTML={{ __html: item.icon }} />
                                    <span>{item.label}</span>
                                </Link>
                            );
                        })}
                    </nav>
                </div>
            </div>
        </aside>
    );
}

export function NotificationList({ notifications }: { notifications: Array<{ id: number; title: string; message: string; is_read: boolean; created_label: string | null }> }) {
    if (notifications.length === 0) {
        return <p className="text-sm text-slate-500">No notifications yet.</p>;
    }

    return (
        <>
            {notifications.map((notification) => (
                <div key={notification.id} className="rounded-2xl border border-slate-200 bg-white px-4 py-3">
                    <div className="flex items-start gap-3">
                        <span className={`mt-1.5 h-2.5 w-2.5 rounded-full ${notification.is_read ? 'bg-slate-300' : 'bg-brand-500'}`} />
                        <div className="min-w-0 flex-1">
                            <div className="flex flex-col gap-1 sm:flex-row sm:items-start sm:justify-between">
                                <p className="font-semibold text-slate-950">{notification.title}</p>
                                <p className="text-xs font-medium text-slate-400">{notification.created_label}</p>
                            </div>
                            <p className="mt-1 text-sm leading-6 text-slate-600">{notification.message}</p>
                        </div>
                    </div>
                </div>
            ))}
        </>
    );
}
