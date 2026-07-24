@props([
    'field' => null,
    'sortField' => null,
    'sortDirection' => null,
    'align' => 'left',
])

@php
    $alignClass = match($align) {
        'right' => 'text-right justify-end',
        'center' => 'text-center justify-center',
        default => 'text-left justify-start',
    };
@endphp

<th {{ $attributes->merge(['class' => 'px-6 py-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/30']) }}>
    @if($field)
        <button type="button" wire:click="sortBy('{{ $field }}')" class="flex items-center gap-1.5 hover:text-slate-800 dark:hover:text-slate-200 transition-all font-semibold {{ $alignClass }} w-full group">
            <span>{{ $slot }}</span>
            @if ($sortField === $field)
                @if ($sortDirection === 'asc')
                    <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/></svg>
                @else
                    <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                @endif
            @else
                <svg class="w-3.5 h-3.5 text-slate-300 dark:text-slate-600 opacity-40 group-hover:opacity-100 transition-opacity" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/></svg>
            @endif
        </button>
    @else
        <div class="flex items-center {{ $alignClass }}">
            {{ $slot }}
        </div>
    @endif
</th>
