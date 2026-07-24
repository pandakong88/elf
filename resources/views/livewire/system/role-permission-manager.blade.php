<div class="space-y-8">
    <!-- Header Page -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-slate-100">Manajemen Akses & Otoritas</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Atur pembagian wewenang ustadz, bendahara, musyrif, dan pengurus pondok secara visual.</p>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="border-b border-slate-200 dark:border-slate-800 flex gap-6">
        <button type="button" wire:click="$set('activeTab', 'roles')" 
                class="pb-4 px-1 border-b-2 text-sm font-bold transition-all {{ $activeTab === 'roles' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400' : 'border-transparent text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-300' }}">
            Hak Akses Peran (Roles)
        </button>
        <button type="button" wire:click="$set('activeTab', 'users')" 
                class="pb-4 px-1 border-b-2 text-sm font-bold transition-all {{ $activeTab === 'users' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400' : 'border-transparent text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-300' }}">
            Penugasan Pengurus (Users)
        </button>
    </div>

    <!-- TAB 1: ROLES & PERMISSIONS GRID -->
    @if($activeTab === 'roles')
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <!-- Left Side: Roles List -->
            <div class="lg:col-span-4 space-y-4">
                <div class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">Daftar Peran Pondok</div>
                
                <div class="space-y-3">
                    @foreach($roles as $role)
                        @php
                            $isSelected = $selectedRoleId == $role->id;
                        @endphp
                        <button type="button" wire:click="selectRole('{{ $role->id }}')"
                                class="w-full text-left p-4 rounded-2xl border transition-all flex items-center justify-between group hover:scale-[1.01] hover:shadow-sm focus:outline-none {{ $isSelected ? 'bg-slate-950 border-l-[5px] border-l-emerald-500 dark:bg-slate-50 border-slate-950 dark:border-slate-50 text-white dark:text-slate-950 shadow-md' : 'bg-white dark:bg-slate-900 border-slate-100 dark:border-slate-800 hover:border-slate-200 dark:hover:border-slate-700 hover:bg-slate-50/50 dark:hover:bg-slate-950/20 text-slate-700 dark:text-slate-300' }}">
                            <div>
                                <div class="flex items-center gap-1.5">
                                    <h3 class="font-bold text-sm tracking-tight capitalize group-hover:text-emerald-500 transition-colors">{{ str_replace('-', ' ', $role->name) }}</h3>
                                    @if($isSelected)
                                        <span class="inline-flex w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    @endif
                                </div>
                                <span class="text-[10px] block mt-0.5 {{ $isSelected ? 'text-slate-400 dark:text-slate-505' : 'text-slate-400 dark:text-slate-500' }}">
                                    {{ $role->users_count }} Pengguna
                                    @if($isSelected)
                                        • <span class="text-emerald-500 font-semibold">Sedang Dikelola</span>
                                    @endif
                                </span>
                            </div>
                            <svg class="w-4 h-4 {{ $isSelected ? 'text-white dark:text-slate-950' : 'text-slate-400 group-hover:translate-x-1 transition-transform' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    @endforeach
                </div>

                <!-- Add New Role Form -->
                <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 p-5 rounded-2xl shadow-sm space-y-3">
                    <label class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Tambah Peran Baru</label>
                    <div class="flex gap-2">
                        <input type="text" wire:model="newRoleName" placeholder="Contoh: ustadz-tahfidz..." 
                               class="flex-1 px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                        <button type="button" wire:click="createRole"
                                class="px-3 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl text-xs font-bold transition-all shadow-sm">
                            Tambah
                        </button>
                    </div>
                    @error('newRoleName')
                        <span class="text-[10px] text-rose-500 font-semibold block mt-1">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Right Side: Permissions Grid for Selected Role -->
            <div class="lg:col-span-8 space-y-6">
                @if($activeRole)
                    <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
                        <!-- Role Detail Title Banner -->
                        <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/20 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <span class="text-xs font-semibold text-emerald-500 dark:text-emerald-400 uppercase tracking-wider">Penyusunan Wewenang</span>
                                <h2 class="text-xl font-bold text-slate-800 dark:text-slate-100 capitalize mt-0.5">Peran: {{ str_replace('-', ' ', $activeRole->name) }}</h2>
                            </div>
                            <div class="flex flex-wrap items-center gap-2">
                                @if($activeRole->name !== 'super-admin')
                                    <button type="button" wire:click="openCopyModal" 
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-lg text-xs font-bold transition-all border border-slate-200 dark:border-slate-700">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/></svg>
                                        <span>Salin Wewenang</span>
                                    </button>
                                    <button type="button" 
                                            wire:click="confirmResetPermissions"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/20 dark:hover:bg-rose-900/30 text-rose-600 dark:text-rose-450 rounded-lg text-xs font-bold transition-all border border-rose-100 dark:border-rose-900/30">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        <span>Reset Wewenang</span>
                                    </button>
                                @endif
                                <x-badge type="info">{{ $activeRole->users_count }} Pengguna Aktif</x-badge>
                            </div>
                        </div>

                        <!-- System Warning for Super-Admin -->
                        @if($activeRole->name === 'super-admin')
                            <div class="p-6 bg-amber-500/10 border-b border-amber-500/20 text-amber-600 dark:text-amber-400 text-xs flex items-start gap-3">
                                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                <div>
                                    <span class="font-bold">Keamanan Sistem:</span> Peran **Super-Admin** memiliki hak akses global penuh secara otomatis oleh sistem. Wewenang untuk peran ini sengaja dikunci demi mencegah terputusnya akses administrasi utama.
                                </div>
                            </div>
                        @endif

                        <!-- Sub-Tabs for Permission Group Filtering -->
                        <div class="px-6 py-3 bg-slate-50/30 dark:bg-slate-950/10 border-b border-slate-100 dark:border-slate-800 flex flex-wrap gap-2 overflow-x-auto">
                            <button type="button" 
                                    wire:click="$set('selectedGroup', 'all')"
                                    class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $selectedGroup === 'all' ? 'bg-emerald-500 text-white shadow-sm' : 'bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300' }}">
                                Semua ({{ count($this->getGroupedPermissions()) }} Kategori)
                            </button>
                            @foreach(array_keys($this->getGroupedPermissions()) as $groupName)
                                @php
                                    $groupPerms = $this->getGroupedPermissions()[$groupName];
                                    $activeCount = 0;
                                    foreach ($groupPerms as $permName => $details) {
                                        if ($activeRole->hasPermissionTo($permName)) {
                                            $activeCount++;
                                        }
                                    }
                                @endphp
                                <button type="button" 
                                        wire:click="$set('selectedGroup', '{{ $groupName }}')"
                                        class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 {{ $selectedGroup === $groupName ? 'bg-emerald-500 text-white shadow-sm' : 'bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300' }}">
                                    <span>{{ $groupName }}</span>
                                    <span class="px-1.5 py-0.5 rounded-md text-[9px] {{ $selectedGroup === $groupName ? 'bg-emerald-600 text-emerald-100' : 'bg-slate-200 dark:bg-slate-700 text-slate-500 dark:text-slate-400' }}">{{ $activeCount }}/{{ count($groupPerms) }}</span>
                                </button>
                            @endforeach
                        </div>

                        <div class="p-6 space-y-8">
                            <!-- Permission Search Bar -->
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400 dark:text-slate-500">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </span>
                                <input type="text" wire:model.live.debounce.300ms="searchPermission" placeholder="Cari berdasarkan nama wewenang atau deskripsi..." 
                                       class="w-full pl-9 pr-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                            </div>

                            <!-- Grouped Permissions -->
                            @forelse($groupedPermissions as $groupName => $perms)
                                <div class="space-y-4">
                                    <h3 class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 pb-2">{{ $groupName }}</h3>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        @foreach($perms as $permName => $details)
                                            @php
                                                $hasPerm = $activeRole->hasPermissionTo($permName);
                                                $disabled = $activeRole->name === 'super-admin';
                                            @endphp
                                            <div class="p-3 border rounded-xl flex items-start justify-between gap-4 transition-all {{ $hasPerm ? 'bg-slate-50/50 dark:bg-slate-950/20 border-emerald-100 dark:border-emerald-900/30' : 'bg-white dark:bg-slate-900 border-slate-100 dark:border-slate-800/80' }}">
                                                <div class="space-y-0.5">
                                                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block">{{ $details['label'] }}</span>
                                                    <span class="text-[10px] text-slate-400 dark:text-slate-500 block leading-normal">{{ $details['desc'] }}</span>
                                                </div>

                                                <!-- Toggle Switch Custom iOS-style -->
                                                <button type="button" 
                                                        wire:click="togglePermission('{{ $permName }}')" 
                                                        @if($disabled) disabled @endif
                                                        class="relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $hasPerm ? 'bg-emerald-500' : 'bg-slate-200 dark:bg-slate-800' }} {{ $disabled ? 'opacity-40 cursor-not-allowed' : '' }}">
                                                    <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $hasPerm ? 'translate-x-4' : 'translate-x-0' }}"></span>
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8 text-slate-400 dark:text-slate-500 text-xs">
                                    Tidak ada wewenang yang cocok dengan pencarian "{{ $searchPermission }}".
                                </div>
                            @endforelse
                        </div>
                    </div>
                @else
                    <div class="text-center py-12 bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 text-slate-405 dark:text-slate-500 shadow-sm">
                        Pilih peran di kolom kiri untuk mulai mengatur wewenang.
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- TAB 2: USER ROLE ASSIGNMENTS -->
    @if($activeTab === 'users')
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
            <!-- Table Filters Header -->
            <div class="p-6 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/20 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between w-full lg:w-auto gap-4 flex-1">
                    <div>
                        <h3 class="text-base font-bold text-slate-800 dark:text-slate-200">Daftar Pengurus & Staf</h3>
                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">Berikan atau batasi akses role bagi personil operasional pondok.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" wire:click="openCreateUserModal" 
                                class="inline-flex items-center gap-1.5 px-3 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl text-xs font-bold transition-all shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            <span>Tambah User</span>
                        </button>
                        <button type="button" wire:click="openImportModal" 
                                class="inline-flex items-center gap-1.5 px-3 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition-all border border-slate-200 dark:border-slate-700">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            <span>Import Excel</span>
                        </button>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto items-stretch sm:items-center">
                    <!-- Dropdown Filter: Peran -->
                    <select wire:model.live="filterRole" class="px-3 py-2 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all cursor-pointer">
                        <option value="">Semua Peran</option>
                        @foreach($roles as $r)
                            <option value="{{ $r->name }}">{{ ucfirst(str_replace('-', ' ', $r->name)) }}</option>
                        @endforeach
                    </select>

                    <!-- Dropdown Filter: Status -->
                    <select wire:model.live="filterStatus" class="px-3 py-2 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all cursor-pointer">
                        <option value="">Semua Status</option>
                        <option value="active">Aktif</option>
                        <option value="inactive">Non-Aktif</option>
                    </select>

                    <!-- Search Input -->
                    <div class="relative flex-1 sm:w-64">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400 dark:text-slate-500">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </span>
                        <input type="text" wire:model.live.debounce.300ms="searchUser" placeholder="Cari nama, email, username..." 
                               class="w-full pl-9 pr-4 py-2 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-550 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/30 dark:bg-slate-950/10">
                            <th class="px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800">Nama Pengurus</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800">Email</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800">Status</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800">Peran Aktif (Roles)</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300 text-sm">
                        @forelse($users as $user)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-950/10 transition-colors">
                                <td class="px-6 py-4 font-bold text-slate-800 dark:text-slate-200">
                                    {{ $user->name }}
                                </td>
                                <td class="px-6 py-4 text-slate-500 dark:text-slate-400">{{ $user->email }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <!-- Toggle Switch iOS-style -->
                                        @php
                                            $isSelf = $user->id === auth()->id();
                                            $isLastSuperAdmin = $user->hasRole('super-admin') && $user->is_active && (\App\Models\User::role('super-admin')->where('is_active', true)->count() <= 1);
                                            $toggleDisabled = $isSelf || $isLastSuperAdmin;
                                        @endphp
                                        <button type="button" 
                                                wire:click="confirmToggleUserStatus('{{ $user->id }}')" 
                                                @if($toggleDisabled) disabled title="{{ $isSelf ? 'Anda tidak dapat menonaktifkan akun sendiri' : 'Satu-satunya Super-Admin aktif harus tetap menyala' }}" @endif
                                                class="relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $user->is_active ? 'bg-emerald-500' : 'bg-slate-200 dark:bg-slate-800' }} {{ $toggleDisabled ? 'opacity-40 cursor-not-allowed' : '' }}">
                                            <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $user->is_active ? 'translate-x-4' : 'translate-x-0' }}"></span>
                                        </button>
                                        <span class="text-xs font-semibold {{ $user->is_active ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                            {{ $user->is_active ? 'Aktif' : 'Non-Aktif' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1.5">
                                        @forelse($user->roles as $role)
                                            <x-badge type="{{ $role->name === 'super-admin' ? 'danger' : ($role->name === 'pengasuh' ? 'success' : 'info') }}">
                                                {{ str_replace('-', ' ', $role->name) }}
                                            </x-badge>
                                        @empty
                                            <span class="text-xs text-slate-400 italic">Belum ada role</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right flex items-center justify-end gap-1.5">
                                    <button type="button" wire:click="openEditUserModal('{{ $user->id }}')" 
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-slate-105 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-lg text-xs font-bold transition-all border border-slate-200 dark:border-slate-700">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        <span>Edit</span>
                                    </button>
                                    <button type="button" wire:click="openUserModal('{{ $user->id }}')" 
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/20 dark:hover:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-lg text-xs font-bold transition-all border border-emerald-100 dark:border-emerald-900/30">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m-3.436-3.436L4 16v4h4l11.436-11.436A2.828 2.828 0 1015.6 3.6l-1.036 1.036z"/></svg>
                                        <span>Role</span>
                                    </button>
                                    <button type="button" 
                                            wire:click="confirmDeleteUser('{{ $user->id }}')" 
                                            @if($user->id === auth()->id()) disabled title="Anda tidak dapat menghapus akun sendiri" class="opacity-40 cursor-not-allowed inline-flex items-center gap-1 px-2.5 py-1.5 bg-rose-50 text-rose-600 rounded-lg text-xs font-bold border border-rose-100" @else class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/20 dark:hover:bg-rose-900/30 text-rose-600 dark:text-rose-450 rounded-lg text-xs font-bold transition-all border border-rose-100 dark:border-rose-900/30" @endif>
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        <span>Hapus</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-400 dark:text-slate-500">
                                    Tidak ada pengurus terdaftar yang sesuai kriteria pencarian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($users->hasPages())
                <div class="flex items-center justify-between px-6 py-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50/30 dark:bg-slate-950/10">
                    <div class="flex justify-between flex-1 sm:hidden">
                        @if ($users->onFirstPage())
                            <span class="inline-flex items-center px-4 py-2 text-xs font-medium text-slate-400 bg-slate-100 dark:bg-slate-800 rounded-xl cursor-not-allowed select-none">
                                Sebelumnya
                            </span>
                        @else
                            <button type="button" wire:click="previousPage('page')" wire:loading.attr="disabled" class="inline-flex items-center px-4 py-2 text-xs font-bold text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-950/50 transition-colors">
                                Sebelumnya
                            </button>
                        @endif

                        @if ($users->hasMorePages())
                            <button type="button" wire:click="nextPage('page')" wire:loading.attr="disabled" class="inline-flex items-center px-4 py-2 ml-3 text-xs font-bold text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-950/50 transition-colors">
                                Selanjutnya
                            </button>
                        @else
                            <span class="inline-flex items-center px-4 py-2 ml-3 text-xs font-medium text-slate-400 bg-slate-100 dark:bg-slate-800 rounded-xl cursor-not-allowed select-none">
                                Selanjutnya
                            </span>
                        @endif
                    </div>
                    
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between gap-4">
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                Menampilkan
                                <span class="font-bold text-slate-800 dark:text-slate-200">{{ $users->firstItem() }}</span>
                                sampai
                                <span class="font-bold text-slate-800 dark:text-slate-200">{{ $users->lastItem() }}</span>
                                dari
                                <span class="font-bold text-slate-800 dark:text-slate-200">{{ $users->total() }}</span>
                                pengurus
                            </p>
                        </div>
                        
                        <div>
                            <nav class="relative z-0 inline-flex -space-x-px rounded-xl shadow-sm overflow-hidden" aria-label="Pagination">
                                {{-- Previous Page Button --}}
                                @if ($users->onFirstPage())
                                    <span class="relative inline-flex items-center px-3 py-2 text-slate-400 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-l-xl cursor-not-allowed">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                                    </span>
                                @else
                                    <button type="button" wire:click="previousPage('page')" wire:loading.attr="disabled" class="relative inline-flex items-center px-3 py-2 text-slate-500 dark:text-slate-400 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-l-xl hover:bg-slate-50 dark:hover:bg-slate-950/50 transition-colors">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                                    </button>
                                @endif

                                {{-- Page Numbers --}}
                                @foreach ($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                                    @if ($page == $users->currentPage())
                                        <span class="relative inline-flex items-center px-3.5 py-2 text-xs font-extrabold text-white bg-emerald-500 border border-emerald-500 z-10">
                                            {{ $page }}
                                        </span>
                                    @else
                                        <button type="button" wire:click="gotoPage({{ $page }}, 'page')" wire:loading.attr="disabled" class="relative inline-flex items-center px-3.5 py-2 text-xs font-bold text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-950/50 transition-colors">
                                            {{ $page }}
                                        </button>
                                    @endif
                                @endforeach

                                {{-- Next Page Button --}}
                                @if ($users->hasMorePages())
                                    <button type="button" wire:click="nextPage('page')" wire:loading.attr="disabled" class="relative inline-flex items-center px-3 py-2 text-slate-500 dark:text-slate-400 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-r-xl hover:bg-slate-50 dark:hover:bg-slate-950/50 transition-colors">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                    </button>
                                @else
                                    <span class="relative inline-flex items-center px-3 py-2 text-slate-400 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-r-xl cursor-not-allowed">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                    </span>
                                @endif
                            </nav>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- TIMELINE FEED LOG AUDIT KEAMANAN -->
    <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl shadow-sm p-6 space-y-6">
        <div>
            <h3 class="text-base font-bold text-slate-800 dark:text-slate-200 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                Log Aktivitas Keamanan Terbaru
            </h3>
            <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">5 aksi administratif sensitif terakhir yang tercatat dalam sistem.</p>
        </div>

        <div class="relative border-l border-slate-100 dark:border-slate-800 ml-3.5 pl-6 space-y-6">
            @forelse($activityLogs as $log)
                <div class="relative">
                    <!-- Dot marker -->
                    <span class="absolute -left-[31px] top-1.5 bg-emerald-500 dark:bg-emerald-400 rounded-full w-2.5 h-2.5 ring-4 ring-white dark:ring-slate-900"></span>
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                        <div>
                            <span class="font-bold text-slate-700 dark:text-slate-200">{{ $log->description }}</span>
                        </div>
                        <div class="text-[10px] text-slate-405 text-slate-400 dark:text-slate-500 flex items-center gap-1.5">
                            <span>Oleh: {{ $log->causer ? $log->causer->name : 'System' }}</span>
                            <span class="text-slate-200 dark:text-slate-800">•</span>
                            <span>{{ $log->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-4 text-slate-400 dark:text-slate-500 text-xs">
                    Belum ada log aktivitas keamanan terekam.
                </div>
            @endforelse
        </div>
    </div>

    <!-- MODAL COPY PERMISSIONS -->
    @if($showCopyModal)
        <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 rounded-3xl w-full max-w-md border border-slate-100 dark:border-slate-800 shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
                <!-- Modal Header -->
                <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100">Salin Wewenang Peran</h3>
                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">Salin wewenang dari peran lain ke peran {{ str_replace('-', ' ', $activeRole->name) }}.</p>
                    </div>
                    <button type="button" wire:click="$set('showCopyModal', false)" class="p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 dark:text-slate-550 transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">Pilih Peran Asal (Sumber)</label>
                        <select wire:model="copyFromRoleId" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                            <option value="">-- Pilih Peran Sumber --</option>
                            @foreach($roles as $r)
                                @if($r->id != $selectedRoleId)
                                    <option value="{{ $r->id }}">{{ ucfirst(str_replace('-', ' ', $r->name)) }} ({{ $r->permissions()->count() }} wewenang)</option>
                                @endif
                            @endforeach
                        </select>
                        @error('copyFromRoleId')
                            <span class="text-[10px] text-rose-500 font-semibold block mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div class="p-4 bg-amber-500/10 border border-amber-500/20 text-amber-600 dark:text-amber-400 text-xs rounded-xl flex items-start gap-3">
                        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <div>
                            <span class="font-bold">Perhatian:</span> Tindakan ini akan **menimpa wewenang peran {{ str_replace('-', ' ', $activeRole->name) }} saat ini**. Wewenang yang telah diatur sebelumnya pada peran ini akan digantikan sepenuhnya oleh wewenang peran asal.
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-950/40 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-2">
                    <button type="button" wire:click="$set('showCopyModal', false)" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-300 font-bold rounded-xl text-xs transition-colors">
                        Batal
                    </button>
                    <button type="button" wire:click="confirmCopyPermissions" class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-xl text-xs transition-all shadow-sm">
                        Salin & Terapkan
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL ASSIGN ROLE TO USER -->
    @if($showUserModal)
        @php
            $editingUser = \App\Models\User::find($editingUserId);
        @endphp
        <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-950 rounded-3xl w-full max-w-md border border-slate-150 dark:border-slate-800 shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
                <!-- Modal Header -->
                <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100">Ubah Penugasan Peran</h3>
                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">Pilih peran yang diemban oleh pengurus.</p>
                    </div>
                    <button type="button" wire:click="closeUserModal" class="p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 dark:text-slate-500 transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6 space-y-5">
                    @if($editingUser)
                        <!-- User Profile Card Summary -->
                        <div class="p-4 bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-slate-800/80 rounded-2xl flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-extrabold flex items-center justify-center text-sm uppercase">
                                {{ substr($editingUser->name, 0, 2) }}
                            </div>
                            <div class="space-y-0.5">
                                <span class="text-sm font-bold text-slate-800 dark:text-slate-200 block leading-tight">{{ $editingUser->name }}</span>
                                <span class="text-[10.5px] text-slate-450 dark:text-slate-500 block leading-tight">{{ $editingUser->email }}</span>
                            </div>
                        </div>
                    @endif

                    <div class="space-y-3">
                        <div class="text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider">Daftar Pilihan Peran</div>
                        
                        <!-- Scrollable list of roles -->
                        <div class="space-y-2.5 max-h-[260px] overflow-y-auto pr-1">
                            @foreach($roles as $role)
                                @php
                                    $hasRole = in_array($role->name, $editingUserRoles);
                                @endphp
                                <div wire:click="toggleUserRole('{{ $role->name }}')"
                                     class="p-3.5 border rounded-2xl flex items-center justify-between cursor-pointer transition-all hover:scale-[1.01] active:scale-[0.99] {{ $hasRole ? 'bg-emerald-500/5 dark:bg-emerald-500/10 border-emerald-500/45 dark:border-emerald-500/40 ring-1 ring-emerald-500/30' : 'bg-white dark:bg-slate-900 border-slate-100 dark:border-slate-800/80 hover:bg-slate-50 dark:hover:bg-slate-950/20' }}">
                                    <div class="flex items-center gap-3">
                                        <!-- Custom checkbox -->
                                        <div class="w-5 h-5 rounded-lg border-2 flex items-center justify-center transition-all {{ $hasRole ? 'bg-emerald-500 border-emerald-500 text-white' : 'border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900' }}">
                                            @if($hasRole)
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                            @endif
                                        </div>
                                        <span class="text-sm font-bold text-slate-700 dark:text-slate-200 capitalize">{{ str_replace('-', ' ', $role->name) }}</span>
                                    </div>
                                    <span class="text-[10px] px-2 py-1 rounded-lg {{ $hasRole ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : 'bg-slate-100 dark:bg-slate-850 text-slate-500 dark:text-slate-450' }} font-extrabold">
                                        {{ $role->users_count }} Anggota
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-950/40 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-2">
                    <button type="button" wire:click="closeUserModal" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-300 font-bold rounded-xl text-xs transition-colors">
                        Batal
                    </button>
                    <button type="button" wire:click="saveUserRoles" class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-xl text-xs transition-all shadow-sm">
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- CUSTOM CONFIRMATION MODAL -->
    @if($showConfirmModal)
        <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-[60] flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 rounded-3xl w-full max-w-sm border border-slate-100 dark:border-slate-800 shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
                <!-- Modal Body -->
                <div class="p-6 text-center space-y-4">
                    <!-- Icon Warning/Danger -->
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-rose-50 dark:bg-rose-950/20 text-rose-500">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    
                    <div class="space-y-2">
                        <h3 class="text-base font-bold text-slate-800 dark:text-slate-100">{{ $confirmTitle }}</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">{{ $confirmDescription }}</p>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-950/40 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-2">
                    <button type="button" wire:click="cancelConfirm" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-300 font-bold rounded-xl text-xs transition-colors">
                        Batal
                    </button>
                    <button type="button" wire:click="executeConfirmedAction" class="px-4 py-2 bg-rose-500 hover:bg-rose-600 text-white font-bold rounded-xl text-xs transition-all shadow-sm">
                        Ya, Lanjutkan
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL CREATE USER -->
    @if($showCreateUserModal)
        <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 rounded-3xl w-full max-w-md border border-slate-100 dark:border-slate-800 shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
                <!-- Modal Header -->
                <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100">Tambah Pengurus / User Baru</h3>
                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">Daftarkan akun login baru untuk staf/pengurus pondok.</p>
                    </div>
                    <button type="button" wire:click="closeCreateUserModal" class="p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 dark:text-slate-500 transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <form wire:submit.prevent="createUser" class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">Nama Lengkap *</label>
                        <input type="text" wire:model="newUserName" placeholder="Nama Lengkap" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                        @error('newUserName') <span class="text-[10px] text-rose-500 font-semibold block mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">Email Login *</label>
                        <input type="email" wire:model="newUserEmail" placeholder="email@ponpes.id" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                        @error('newUserEmail') <span class="text-[10px] text-rose-500 font-semibold block mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">Username (Opsional)</label>
                        <input type="text" wire:model="newUserUsername" placeholder="username" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                        @error('newUserUsername') <span class="text-[10px] text-rose-500 font-semibold block mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">Password *</label>
                            <input type="password" wire:model="newUserPassword" placeholder="••••••••" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                            @error('newUserPassword') <span class="text-[10px] text-rose-500 font-semibold block mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">Konfirmasi PW *</label>
                            <input type="password" wire:model="newUserPasswordConfirm" placeholder="••••••••" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                            @error('newUserPasswordConfirm') <span class="text-[10px] text-rose-500 font-semibold block mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">Pilih Peran (Roles) *</label>
                        <div class="grid grid-cols-2 gap-2 max-h-[140px] overflow-y-auto border border-slate-200 dark:border-slate-800 rounded-xl p-3 bg-slate-50 dark:bg-slate-950">
                            @foreach($roles as $role)
                                <label class="flex items-center gap-2 text-xs text-slate-705 dark:text-slate-300 cursor-pointer">
                                    <input type="checkbox" wire:model="newUserRoles" value="{{ $role->name }}" class="rounded text-emerald-500 border-slate-300 focus:ring-emerald-500">
                                    <span class="capitalize">{{ str_replace('-', ' ', $role->name) }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('newUserRoles') <span class="text-[10px] text-rose-500 font-semibold block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Modal Footer -->
                    <div class="pt-4 border-t border-slate-150 dark:border-slate-800 flex justify-end gap-2">
                        <button type="button" wire:click="closeCreateUserModal" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-300 font-bold rounded-xl text-xs transition-colors">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-xl text-xs transition-all shadow-sm">Simpan & Tambahkan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- MODAL EDIT USER -->
    @if($showEditUserModal)
        <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 rounded-3xl w-full max-w-md border border-slate-100 dark:border-slate-800 shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
                <!-- Modal Header -->
                <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100">Edit Data Pengurus</h3>
                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">Ubah biodata login atau password akun.</p>
                    </div>
                    <button type="button" wire:click="closeEditUserModal" class="p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 dark:text-slate-500 transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <form wire:submit.prevent="updateUser" class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">Nama Lengkap *</label>
                        <input type="text" wire:model="editName" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                        @error('editName') <span class="text-[10px] text-rose-500 font-semibold block mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">Email Login *</label>
                        <input type="email" wire:model="editEmail" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                        @error('editEmail') <span class="text-[10px] text-rose-500 font-semibold block mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">Username</label>
                        <input type="text" wire:model="editUsername" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                        @error('editUsername') <span class="text-[10px] text-rose-500 font-semibold block mt-1">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="p-4 bg-slate-50 dark:bg-slate-950 rounded-2xl border border-slate-100 dark:border-slate-800 space-y-3">
                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 block font-bold">Ubah Password (Kosongkan jika tidak diganti)</span>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Password Baru</label>
                                <input type="password" wire:model="editPassword" placeholder="••••••••" class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                                @error('editPassword') <span class="text-[10px] text-rose-500 font-semibold block mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Konfirmasi PW</label>
                                <input type="password" wire:model="editPasswordConfirm" placeholder="••••••••" class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                                @error('editPasswordConfirm') <span class="text-[10px] text-rose-500 font-semibold block mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <label class="block text-xs font-bold text-slate-405 dark:text-slate-500 uppercase tracking-wider">Status Akun:</label>
                        <div class="flex items-center gap-4">
                            <label class="flex items-center gap-1.5 text-xs text-slate-700 dark:text-slate-300 cursor-pointer">
                                <input type="radio" wire:model="editIsActive" value="1" class="text-emerald-500 focus:ring-emerald-500">
                                <span>Aktif</span>
                            </label>
                            <label class="flex items-center gap-1.5 text-xs text-slate-700 dark:text-slate-300 cursor-pointer">
                                <input type="radio" wire:model="editIsActive" value="0" class="text-rose-500 focus:ring-rose-500">
                                <span>Non-Aktif</span>
                            </label>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="pt-4 border-t border-slate-150 dark:border-slate-800 flex justify-end gap-2">
                        <button type="button" wire:click="closeEditUserModal" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-300 font-bold rounded-xl text-xs transition-colors">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-xl text-xs transition-all shadow-sm">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- MODAL DELETE USER -->
    @if($showDeleteUserModal)
        <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-[60] flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 rounded-3xl w-full max-w-sm border border-slate-100 dark:border-slate-800 shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
                <!-- Modal Body -->
                <div class="p-6 text-center space-y-4">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-rose-50 dark:bg-rose-950/20 text-rose-500">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </div>
                    
                    <div class="space-y-2">
                        <h3 class="text-base font-bold text-slate-800 dark:text-slate-100">Hapus Akun Pengurus?</h3>
                        <p class="text-xs text-slate-505 dark:text-slate-400 leading-relaxed">
                            Apakah Anda yakin ingin menghapus akun pengurus <span class="font-extrabold text-slate-800 dark:text-white">"{{ $deletingUserName }}"</span> secara permanen?
                            Tindakan ini tidak dapat dibatalkan.
                        </p>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-950/40 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-2">
                    <button type="button" wire:click="$set('showDeleteUserModal', false)" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-300 font-bold rounded-xl text-xs transition-colors">Batal</button>
                    <button type="button" wire:click="deleteUser" class="px-4 py-2 bg-rose-500 hover:bg-rose-600 text-white font-bold rounded-xl text-xs transition-all shadow-sm">Ya, Hapus Akun</button>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL IMPORT EXCEL -->
    @if($showImportModal)
        <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 rounded-3xl w-full max-w-lg border border-slate-100 dark:border-slate-800 shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
                <!-- Modal Header -->
                <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100">Import User Pengurus</h3>
                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">Tambah banyak user pengurus sekaligus via Excel.</p>
                    </div>
                    <button type="button" wire:click="closeImportModal" class="p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 dark:text-slate-500 transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6 space-y-6">
                    <!-- Step 1: Download template -->
                    <div class="p-4 bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-100 dark:border-emerald-900/30 rounded-2xl flex items-center justify-between gap-4">
                        <div>
                            <span class="text-xs font-bold text-emerald-800 dark:text-emerald-300 block">Langkah 1: Unduh Template Excel</span>
                            <span class="text-[10px] text-emerald-600 dark:text-emerald-500 block mt-0.5">Gunakan format resmi agar import terbaca sistem.</span>
                        </div>
                        <a href="{{ route('system.users.download-template') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl text-xs font-bold transition-all shadow-sm">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            <span>Unduh Template</span>
                        </a>
                    </div>

                    <!-- Step 2: Upload Excel file -->
                    <div class="space-y-2">
                        <span class="text-xs font-bold text-slate-500 dark:text-slate-450 block">Langkah 2: Isi & Upload Template</span>
                        <div class="relative border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-3xl p-8 text-center hover:border-emerald-500/50 transition-all bg-slate-50/50 dark:bg-slate-950/10">
                            <input type="file" wire:model="importFile" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept=".xlsx,.xls">
                            
                            <div class="space-y-2">
                                <div class="mx-auto h-12 w-12 text-slate-400 dark:text-slate-500 flex items-center justify-center">
                                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V4a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                                </div>
                                @if($importFile)
                                    <span class="text-xs font-bold text-slate-700 dark:text-slate-350 block">Terpilih: {{ $importFile->getClientOriginalName() }}</span>
                                    <span class="text-[10px] text-slate-400 block">Sedang diproses...</span>
                                @else
                                    <span class="text-xs font-bold text-slate-700 dark:text-slate-300 block">Seret file template atau klik untuk memilih</span>
                                    <span class="text-[10px] text-slate-400 block">Format yang didukung: .xlsx, .xls (Maks. 2MB)</span>
                                @endif
                            </div>
                        </div>
                        @error('importFile') <span class="text-[10px] text-rose-500 font-semibold block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Step 3: Result Summary & Preview -->
                    @if($importDone)
                        <div class="space-y-4">
                            <div class="flex items-center justify-between text-xs border-b border-slate-100 dark:border-slate-800 pb-2">
                                <span class="font-bold text-slate-700 dark:text-slate-350">Pratinjau Berkas Excel:</span>
                                <div class="flex gap-3 font-bold">
                                    <span class="text-emerald-600 dark:text-emerald-450">✅ {{ count($tempValidUsers) }} Siap Di-import</span>
                                    <span class="text-rose-600 dark:text-rose-455">❌ {{ count($tempInvalidUsers) }} Bermasalah</span>
                                </div>
                            </div>

                            <!-- List of Valid Users -->
                            @if(!empty($tempValidUsers))
                                <div class="border border-emerald-100 dark:border-emerald-900/35 rounded-2xl p-4 bg-emerald-500/[0.02] dark:bg-emerald-500/[0.01] space-y-2.5 max-h-[160px] overflow-y-auto">
                                    <span class="text-[10px] font-extrabold text-emerald-650 dark:text-emerald-400 uppercase tracking-wider block">Siap Di-import ({{ count($tempValidUsers) }} User)</span>
                                    <div class="divide-y divide-emerald-500/10 dark:divide-emerald-500/5 text-xs text-slate-700 dark:text-slate-300">
                                        @foreach($tempValidUsers as $vu)
                                            <div class="py-2 flex items-center justify-between gap-3 first:pt-0 last:pb-0">
                                                <div>
                                                    <span class="font-bold text-slate-800 dark:text-slate-100">{{ $vu['name'] }}</span>
                                                    <span class="text-[10px] text-slate-400 dark:text-slate-500 block mt-0.5">{{ $vu['email'] }}</span>
                                                </div>
                                                <div class="flex flex-wrap gap-1">
                                                    @foreach($vu['roles'] as $role)
                                                        <span class="px-1.5 py-0.5 rounded bg-slate-105 dark:bg-slate-800 text-slate-600 dark:text-slate-400 text-[9px] font-bold capitalize">{{ str_replace('-', ' ', $role) }}</span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- List of Invalid Users -->
                            @if(!empty($tempInvalidUsers))
                                <div class="border border-rose-100 dark:border-rose-950/30 rounded-2xl p-4 bg-rose-500/[0.02] dark:bg-rose-500/[0.01] space-y-2.5 max-h-[160px] overflow-y-auto">
                                    <span class="text-[10px] font-extrabold text-rose-600 dark:text-rose-455 uppercase tracking-wider block">Bermasalah / Akan Dilewati ({{ count($tempInvalidUsers) }} User)</span>
                                    <div class="divide-y divide-rose-500/10 dark:divide-rose-500/5 text-xs text-slate-750 dark:text-slate-350">
                                        @foreach($tempInvalidUsers as $err)
                                            <div class="py-2 space-y-1 first:pt-0 last:pb-0">
                                                <div class="flex items-center justify-between text-[11px] font-bold text-slate-800 dark:text-slate-200">
                                                    <span>Baris {{ $err['row'] }}: {{ $err['name'] }}</span>
                                                    <span class="text-[9px] text-rose-500/90 font-medium font-mono">{{ $err['email'] }}</span>
                                                </div>
                                                <ul class="list-disc list-inside text-[10px] text-rose-500/90 pl-1 space-y-0.5">
                                                    @foreach($err['errors'] as $msg)
                                                        <li>{{ $msg }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-950/40 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-2">
                    <button type="button" wire:click="closeImportModal" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-300 font-bold rounded-xl text-xs transition-colors">Batal</button>
                    @if($importDone && count($tempValidUsers) > 0)
                        <button type="button" wire:click="confirmAndSaveImport" class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-xl text-xs transition-all shadow-sm">
                            Impor Sekarang ({{ count($tempValidUsers) }} User)
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
