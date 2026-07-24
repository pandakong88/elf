@props([
    'type' => 'neutral',
])

@php
    $classes = match($type) {
        'success', 'green', 'approved', 'active', 'out' => 'bg-emerald-50 text-emerald-700 border-emerald-100/80 dark:bg-emerald-950/30 dark:text-emerald-400 dark:border-emerald-900/50',
        'danger', 'red', 'rejected', 'inactive', 'violation' => 'bg-rose-50 text-rose-700 border-rose-100/80 dark:bg-rose-950/30 dark:text-rose-400 dark:border-rose-900/50',
        'warning', 'amber', 'pending', 'resolved_late' => 'bg-amber-50 text-amber-700 border-amber-100/80 dark:bg-amber-950/30 dark:text-amber-400 dark:border-amber-900/50',
        'info', 'blue', 'primary', 'in_room' => 'bg-blue-50 text-blue-700 border-blue-100/80 dark:bg-blue-950/30 dark:text-blue-400 dark:border-blue-900/50',
        default => 'bg-slate-100 text-slate-700 border-slate-200/80 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700/80',
    };
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border ' . $classes]) }}>
    {{ $slot }}
</span>
