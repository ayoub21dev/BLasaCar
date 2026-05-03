<aside class="hidden w-72 shrink-0 md:block">
    <div class="sticky top-24 space-y-6">
        <div class="surface-soft p-6">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ $label }}</p>
            <nav class="mt-5 space-y-2">
                @foreach ($items as $item)
                    <a href="{{ route($item['route']) }}"
                       class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium transition {{ request()->routeIs($item['route']) ? 'bg-brand-50 text-brand-700' : 'text-slate-600 hover:bg-slate-50 hover:text-brand-700' }}">
                        {!! $item['icon'] !!}
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>
        </div>
    </div>
</aside>
