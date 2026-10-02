<div class="relative" x-data="{ open: false }" wire:poll.30s>
    {{-- Bell Button --}}
    <button type="button" 
            @click="open = !open" 
            class="relative p-2 text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all focus:outline-none"
            :class="{ 'bg-slate-100 dark:bg-slate-800 text-emerald-600 dark:text-emerald-400': open }"
            title="Pemberitahuan & Notifikasi">
        
        {{-- Bell SVG Icon --}}
        <svg class="w-5 h-5 transition-transform duration-200" :class="{ 'scale-110': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>

        {{-- Unread Badge --}}
        @if($unreadCount > 0)
            <span class="absolute 1.5 top-1.5 right-1.5 w-2 h-2 rounded-full bg-rose-500 animate-ping"></span>
            <span class="absolute -top-1 -right-1 px-1.5 py-0.5 min-w-[18px] text-[10px] font-black leading-none text-white bg-rose-500 rounded-full flex items-center justify-center shadow-md">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    {{-- Notification Dropdown --}}
    <div x-show="open" 
         @click.away="open = false"
         @keydown.escape.window="open = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 translate-y-2"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-2"
         x-cloak
         class="absolute right-0 mt-2.5 w-80 sm:w-96 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl z-50 overflow-hidden text-slate-800 dark:text-slate-200">
        
        {{-- Header Dropdown --}}
        <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-950/40">
            <div class="flex items-center gap-2">
                <span class="font-bold text-xs uppercase tracking-wider text-slate-700 dark:text-slate-300">Notifikasi</span>
                @if($unreadCount > 0)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300">
                        {{ $unreadCount }} Baru
                    </span>
                @endif
            </div>

            @if($unreadCount > 0)
                <button type="button" 
                        wire:click="markAllAsRead"
                        class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 transition-colors">
                    Tandai Semua Dibaca
                </button>
            @endif
        </div>

        {{-- Notification List --}}
        <div class="max-h-[380px] overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800/60">
            @forelse($notifications as $notification)
                @php
                    $isUnread = is_null($notification->read_at);
                    $data = $notification->data ?? [];
                    $category = $data['category'] ?? 'transfer';
                    $createdAt = \Carbon\Carbon::parse($notification->created_at)->locale('id');
                @endphp

                <div wire:click="openNotification('{{ $notification->id }}')"
                     class="p-3.5 flex items-start gap-3 hover:bg-slate-50 dark:hover:bg-slate-800/60 cursor-pointer transition-colors relative {{ $isUnread ? 'bg-emerald-50/20 dark:bg-emerald-950/10' : '' }}">
                    
                    {{-- Category Icon --}}
                    <div class="shrink-0 w-8 h-8 rounded-xl flex items-center justify-center text-sm {{ $isUnread ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400' }}">
                        @if($category === 'transfer')
                            💰
                        @elseif($category === 'warning')
                            ⚠️
                        @else
                            🔔
                        @endif
                    </div>

                    {{-- Body Content --}}
                    <div class="flex-1 min-w-0 space-y-0.5">
                        <div class="flex items-center justify-between gap-1">
                            <h4 class="text-xs font-bold text-slate-900 dark:text-slate-100 truncate {{ $isUnread ? 'text-emerald-700 dark:text-emerald-300' : '' }}">
                                {{ $data['title'] ?? 'Pemberitahuan Sistem' }}
                            </h4>
                            <span class="text-[10px] text-slate-400 whitespace-nowrap shrink-0">
                                {{ $createdAt->diffForHumans() }}
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-600 dark:text-slate-400 line-clamp-2 leading-relaxed">
                            {{ $data['body'] ?? '' }}
                        </p>
                    </div>

                    {{-- Unread Dot --}}
                    @if($isUnread)
                        <div class="shrink-0 self-center">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 block"></span>
                        </div>
                    @endif
                </div>
            @empty
                <div class="p-8 text-center space-y-2">
                    <div class="w-12 h-12 mx-auto rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 text-xl">
                        🔔
                    </div>
                    <p class="text-xs font-bold text-slate-700 dark:text-slate-300">Belum Ada Notifikasi</p>
                    <p class="text-[11px] text-slate-400 max-w-[200px] mx-auto">
                        Pemberitahuan transfer manual santri dan update sistem akan muncul di sini.
                    </p>
                </div>
            @endforelse
        </div>

        {{-- Footer --}}
        <div class="p-2 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 text-center">
            <a href="{{ route('keuangan.billing') }}?tab=transfers" 
               @click="open = false"
               class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 py-1 transition-colors">
                <span>Buka Verifikasi Transfer</span>
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </a>
        </div>
    </div>
</div>
