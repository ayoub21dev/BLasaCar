@forelse ($notifications as $notification)
    <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3">
        <div class="flex items-start gap-3">
            <span class="mt-1.5 h-2.5 w-2.5 rounded-full {{ $notification->is_read ? 'bg-slate-300' : 'bg-brand-500' }}"></span>
            <div class="min-w-0 flex-1">
                <div class="flex flex-col gap-1 sm:flex-row sm:items-start sm:justify-between">
                    <p class="font-semibold text-slate-950">{{ $notification->title }}</p>
                    <p class="text-xs font-medium text-slate-400">{{ $notification->created_at?->diffForHumans() }}</p>
                </div>
                <p class="mt-1 text-sm leading-6 text-slate-600">{{ $notification->message }}</p>
            </div>
        </div>
    </div>
@empty
    <p class="text-sm text-slate-500">No notifications yet.</p>
@endforelse
