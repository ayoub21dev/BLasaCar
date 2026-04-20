@php
    $map = [
        'scheduled' => 'bg-sky-100 text-sky-700',
        'pending' => 'bg-amber-100 text-amber-700',
        'confirmed' => 'bg-emerald-100 text-emerald-700',
        'completed' => 'bg-emerald-100 text-emerald-700',
        'cancelled' => 'bg-rose-100 text-rose-700',
        'active' => 'bg-emerald-100 text-emerald-700',
        'suspended' => 'bg-rose-100 text-rose-700',
    ];

    $classes = $map[$status] ?? 'bg-slate-100 text-slate-700';
@endphp

<span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $classes }}">
    {{ $label ?? ucfirst(str_replace('_', ' ', $status)) }}
</span>
