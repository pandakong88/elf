@props([
    'title' => null,
    'subtitle' => null,
    'headerClass' => '',
    'bodyClass' => '',
    'footerClass' => '',
])

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden transition-all duration-300 hover:shadow-md']) }}>
    @if ($title || isset($header))
        <div class="px-6 py-4 border-b border-slate-50 dark:border-slate-800 flex items-center justify-between {{ $headerClass }}">
            @if ($title)
                <div>
                    <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 tracking-tight">{{ $title }}</h3>
                    @if ($subtitle)
                        <p class="text-xs text-slate-400 dark:text-slate-500 font-normal mt-0.5">{{ $subtitle }}</p>
                    @endif
                </div>
            @else
                {{ $header }}
            @endif
        </div>
    @endif

    <div class="p-6 {{ $bodyClass }}">
        {{ $slot }}
    </div>

    @if (isset($footer))
        <div class="px-6 py-4 bg-slate-50/50 dark:bg-slate-950/30 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-2 {{ $footerClass }}">
            {{ $footer }}
        </div>
    @endif
</div>
