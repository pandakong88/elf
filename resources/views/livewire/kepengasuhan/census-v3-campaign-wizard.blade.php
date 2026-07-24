<div class="py-6 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Wizard Progress Steps -->
        <div class="mb-10">
            <div class="flex items-center justify-between relative">
                <!-- Background Progress Line -->
                <div class="absolute left-0 top-1/2 -translate-y-1/2 w-full h-1 bg-slate-200 dark:bg-slate-800 -z-10 rounded-full"></div>
                <!-- Active Progress Line -->
                <div class="absolute left-0 top-1/2 -translate-y-1/2 h-1 bg-emerald-500 -z-10 rounded-full transition-all duration-300" style="width: {{ (($currentStep - 1) / ($totalSteps - 1)) * 100 }}%"></div>

                <!-- Step 1 -->
                <div class="flex flex-col items-center">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm shadow-md transition-all duration-300 {{ $currentStep >= 1 ? 'bg-emerald-600 text-white shadow-emerald-500/20' : 'bg-white dark:bg-slate-900 border-2 border-slate-300 dark:border-slate-700 text-slate-400' }}">1</div>
                    <span class="text-xs font-semibold mt-2 {{ $currentStep >= 1 ? 'text-slate-800 dark:text-white' : 'text-slate-400' }}">Info Dasar</span>
                </div>

                <!-- Step 2 -->
                <div class="flex flex-col items-center">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm shadow-md transition-all duration-300 {{ $currentStep >= 2 ? 'bg-emerald-600 text-white shadow-emerald-500/20' : 'bg-white dark:bg-slate-900 border-2 border-slate-300 dark:border-slate-700 text-slate-400' }}">2</div>
                    <span class="text-xs font-semibold mt-2 {{ $currentStep >= 2 ? 'text-slate-800 dark:text-white' : 'text-slate-400' }}">Target</span>
                </div>

                <!-- Step 3 -->
                <div class="flex flex-col items-center">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm shadow-md transition-all duration-300 {{ $currentStep >= 3 ? 'bg-emerald-600 text-white shadow-emerald-500/20' : 'bg-white dark:bg-slate-900 border-2 border-slate-300 dark:border-slate-700 text-slate-400' }}">3</div>
                    <span class="text-xs font-semibold mt-2 {{ $currentStep >= 3 ? 'text-slate-800 dark:text-white' : 'text-slate-400' }}">Metode &amp; Petugas</span>
                </div>

                <!-- Step 4 -->
                <div class="flex flex-col items-center">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm shadow-md transition-all duration-300 {{ $currentStep >= 4 ? 'bg-emerald-600 text-white shadow-emerald-500/20' : 'bg-white dark:bg-slate-900 border-2 border-slate-300 dark:border-slate-700 text-slate-400' }}">4</div>
                    <span class="text-xs font-semibold mt-2 {{ $currentStep >= 4 ? 'text-slate-800 dark:text-white' : 'text-slate-400' }}">Review</span>
                </div>
            </div>
        </div>

        <!-- Wizard Card -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden mb-6">
            <div class="p-6 sm:p-8">
                <!-- STEP 1: INFO DASAR -->
                @if ($currentStep === 1)
                    <div class="space-y-6">
                        <div>
                            <h2 class="text-xl font-bold text-slate-800 dark:text-white">Informasi Dasar Sensus</h2>
                            <p class="text-sm text-slate-400">Tentukan nama, periode, dan template formulir sensus.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label for="name" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Kampanye Sensus <span class="text-rose-500">*</span></label>
                                <input type="text" id="name" wire:model="name" placeholder="Contoh: Sensus Bulanan Juli 2026" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-colors">
                                @error('name') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="month" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Bulan Sensus <span class="text-rose-500">*</span></label>
                                <select id="month" wire:model="month" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-colors">
                                    <option value="1">Januari</option>
                                    <option value="2">Februari</option>
                                    <option value="3">Maret</option>
                                    <option value="4">April</option>
                                    <option value="5">Mei</option>
                                    <option value="6">Juni</option>
                                    <option value="7">Juli</option>
                                    <option value="8">Agustus</option>
                                    <option value="9">September</option>
                                    <option value="10">Oktober</option>
                                    <option value="11">November</option>
                                    <option value="12">Desember</option>
                                </select>
                                @error('month') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="year" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Tahun Sensus <span class="text-rose-500">*</span></label>
                                <input type="number" id="year" wire:model="year" min="2020" max="2100" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-colors">
                                @error('year') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="template_id" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Template Sensus <span class="text-rose-500">*</span></label>
                                <select id="template_id" wire:model="template_id" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-colors">
                                    <option value="">-- Pilih Template Sensus --</option>
                                    @foreach ($templates as $tpl)
                                        <option value="{{ $tpl->id }}">{{ $tpl->name }}</option>
                                    @endforeach
                                </select>
                                @error('template_id') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror

                                @if ($template_id)
                                    @php
                                        $selectedTpl = $templates->firstWhere('id', $template_id);
                                    @endphp
                                    @if ($selectedTpl)
                                        <div class="mt-3 p-3 bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-slate-800 rounded-2xl text-xs space-y-1.5 text-slate-500 dark:text-slate-400">
                                            <p class="font-bold text-slate-700 dark:text-slate-300">{{ $selectedTpl->name }}</p>
                                            <p>{{ $selectedTpl->description ?: 'Tidak ada deskripsi.' }}</p>
                                            <p class="text-emerald-500 font-medium">Memiliki {{ $selectedTpl->fields->count() }} kolom isian.</p>
                                        </div>
                                    @endif
                                @endif
                            </div>

                            <div class="md:col-span-2">
                                <label for="description" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Keterangan Tambahan</label>
                                <textarea id="description" wire:model="description" rows="3" placeholder="Contoh: Sensus untuk mendata keikutsertaan wisuda tahfidz..." class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-colors"></textarea>
                                @error('description') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                @endif

                <!-- STEP 2: TARGET SCOPE -->
                @if ($currentStep === 2)
                    <div class="space-y-6">
                        <div>
                            <h2 class="text-xl font-bold text-slate-800 dark:text-white">Target Asrama Sensus</h2>
                            <p class="text-sm text-slate-400">Pilih asrama mana saja yang akan disensus.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Options -->
                            <div class="space-y-3">
                                <label class="flex items-start gap-3 p-4 bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-slate-800/80 rounded-2xl cursor-pointer hover:border-slate-250 dark:hover:border-slate-700 transition-all">
                                    <input type="radio" wire:model="target_scope" value="all" class="mt-1 text-emerald-600 border-slate-300 focus:ring-emerald-500">
                                    <div>
                                        <span class="block font-bold text-sm text-slate-800 dark:text-white">Semua Asrama</span>
                                        <span class="block text-xs text-slate-400">Sensus mencakup seluruh asrama putra dan putri.</span>
                                    </div>
                                </label>

                                <label class="flex items-start gap-3 p-4 bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-slate-800/80 rounded-2xl cursor-pointer hover:border-slate-250 dark:hover:border-slate-700 transition-all">
                                    <input type="radio" wire:model="target_scope" value="putra" class="mt-1 text-emerald-600 border-slate-300 focus:ring-emerald-500">
                                    <div>
                                        <span class="block font-bold text-sm text-slate-800 dark:text-white">Hanya Komplek Putra</span>
                                        <span class="block text-xs text-slate-400">Hanya asrama laki-laki yang menjadi target.</span>
                                    </div>
                                </label>

                                <label class="flex items-start gap-3 p-4 bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-slate-800/80 rounded-2xl cursor-pointer hover:border-slate-250 dark:hover:border-slate-700 transition-all">
                                    <input type="radio" wire:model="target_scope" value="putri" class="mt-1 text-emerald-600 border-slate-300 focus:ring-emerald-500">
                                    <div>
                                        <span class="block font-bold text-sm text-slate-800 dark:text-white">Hanya Komplek Putri</span>
                                        <span class="block text-xs text-slate-400">Hanya asrama perempuan yang menjadi target.</span>
                                    </div>
                                </label>

                                <label class="flex items-start gap-3 p-4 bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-slate-800/80 rounded-2xl cursor-pointer hover:border-slate-250 dark:hover:border-slate-700 transition-all">
                                    <input type="radio" wire:model="target_scope" value="custom_dormitories" class="mt-1 text-emerald-600 border-slate-300 focus:ring-emerald-500">
                                    <div>
                                        <span class="block font-bold text-sm text-slate-800 dark:text-white">Pilih Asrama Kustom</span>
                                        <span class="block text-xs text-slate-400">Pilih secara spesifik komplek/asrama tertentu.</span>
                                    </div>
                                </label>
                            </div>

                            <!-- Custom Dormitories Selector -->
                            <div class="p-4 bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-slate-800 rounded-2xl">
                                @if ($target_scope === 'custom_dormitories')
                                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Pilih Asrama:</h4>
                                    <div class="space-y-2 max-h-64 overflow-y-auto pr-1">
                                        @foreach ($dormitories as $dorm)
                                            <label class="flex items-center gap-2.5 p-2 hover:bg-slate-100 dark:hover:bg-slate-900 rounded-xl cursor-pointer transition-all">
                                                <input type="checkbox" wire:model="target_dormitory_ids" value="{{ $dorm->id }}" class="w-4 h-4 rounded text-emerald-600 border-slate-300 focus:ring-emerald-500">
                                                <div class="text-xs">
                                                    <span class="font-bold text-slate-800 dark:text-white">{{ $dorm->name }}</span>
                                                    <span class="text-slate-400 ml-1">({{ $dorm->gender === 'L' ? 'Putra' : 'Putri' }})</span>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                    @error('target_dormitory_ids') <p class="text-rose-500 text-xs mt-2 font-medium">{{ $message }}</p> @enderror
                                @else
                                    <div class="h-full flex items-center justify-center text-center p-6 text-slate-400">
                                        <div>
                                            <div class="text-slate-400 mb-2 flex justify-center"><svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg></div>
                                            <p class="text-xs mt-2">Target otomatis ditentukan berdasarkan filter di sebelah kiri.</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <!-- STEP 3: METODE & PETUGAS -->
                @if ($currentStep === 3)
                    <div class="space-y-6">
                        <div>
                            <h2 class="text-xl font-bold text-slate-800 dark:text-white">Metode Distribusi &amp; Penugasan</h2>
                            <p class="text-sm text-slate-400">Tentukan cara penginputan dan siapa pengisi data asrama.</p>
                        </div>

                        <!-- Workflow Mode selection -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-3">
                                <label class="flex items-start gap-3 p-4 bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-slate-800/80 rounded-2xl cursor-pointer hover:border-slate-250 dark:hover:border-slate-700 transition-all">
                                    <input type="radio" wire:model="workflow_mode" value="distributed" class="mt-1 text-emerald-600 border-slate-300 focus:ring-emerald-500">
                                    <div>
                                        <span class="block font-bold text-sm text-slate-800 dark:text-white">Distribusi ke Musyrif</span>
                                        <span class="block text-xs text-slate-400">Sensus terkirim ke akun masing-masing musyrif/ketua komplek.</span>
                                    </div>
                                </label>

                                <label class="flex items-start gap-3 p-4 bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-slate-800/80 rounded-2xl cursor-pointer hover:border-slate-250 dark:hover:border-slate-700 transition-all">
                                    <input type="radio" wire:model="workflow_mode" value="admin_only" class="mt-1 text-emerald-600 border-slate-300 focus:ring-emerald-500">
                                    <div>
                                        <span class="block font-bold text-sm text-slate-800 dark:text-white">Admin Input Sendiri</span>
                                        <span class="block text-xs text-slate-400">Hanya admin pusat yang memiliki hak akses mengisi lembar sensus.</span>
                                    </div>
                                </label>

                                <label class="flex items-start gap-3 p-4 bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-slate-800/80 rounded-2xl cursor-pointer hover:border-slate-250 dark:hover:border-slate-700 transition-all">
                                    <input type="radio" wire:model="workflow_mode" value="excel" class="mt-1 text-emerald-600 border-slate-300 focus:ring-emerald-500">
                                    <div>
                                        <span class="block font-bold text-sm text-slate-800 dark:text-white">Menggunakan Unduh/Unggah Excel</span>
                                        <span class="block text-xs text-slate-400">Metode input offline menggunakan template Excel dinamis.</span>
                                    </div>
                                </label>

                                <label class="flex items-start gap-3 p-4 bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-slate-800/80 rounded-2xl cursor-pointer hover:border-slate-250 dark:hover:border-slate-700 transition-all">
                                    <input type="radio" wire:model="workflow_mode" value="hybrid" class="mt-1 text-emerald-600 border-slate-300 focus:ring-emerald-500">
                                    <div>
                                        <span class="block font-bold text-sm text-slate-800 dark:text-white">Hybrid (Distribusi + Excel)</span>
                                        <span class="block text-xs text-slate-400">Musyrif dapat memilih input langsung di web maupun via Excel.</span>
                                    </div>
                                </label>
                            </div>

                            <!-- Options toggles -->
                            <div class="p-5 bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-slate-800 rounded-2xl space-y-4">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Parameter Tambahan:</h4>
                                
                                <div class="flex items-center gap-3">
                                    <input type="checkbox" id="allow_direct_input" wire:model="allow_direct_input" class="w-4 h-4 rounded text-emerald-600 border-slate-300 focus:ring-emerald-500">
                                    <label for="allow_direct_input" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Izinkan pengisian lembar di Web</label>
                                </div>

                                <div class="flex items-center gap-3">
                                    <input type="checkbox" id="allow_excel" wire:model="allow_excel" class="w-4 h-4 rounded text-emerald-600 border-slate-300 focus:ring-emerald-500">
                                    <label for="allow_excel" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Sediakan opsi unduh / unggah Excel</label>
                                </div>
                                @error('allow_direct_input') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Petugas Assignment (if workflow is distributed or hybrid) -->
                        @if ($workflow_mode === 'distributed' || $workflow_mode === 'hybrid')
                            <div class="border-t border-slate-150 dark:border-slate-800 pt-6">
                                <h3 class="text-sm font-bold text-slate-800 dark:text-white mb-3">Tugaskan Ketua Komplek / Musyrif</h3>
                                <div class="bg-slate-50 dark:bg-slate-950 rounded-2xl border border-slate-100 dark:border-slate-800 overflow-hidden">
                                    <table class="w-full text-left border-collapse text-xs">
                                        <thead>
                                            <tr class="bg-slate-100 dark:bg-slate-900 text-slate-500 font-bold">
                                                <th class="p-3">Nama Asrama</th>
                                                <th class="p-3">Gender</th>
                                                <th class="p-3">Ketua / Penanggung Jawab</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                                            @forelse ($targetedDormitories as $dorm)
                                                <tr>
                                                    <td class="p-3 font-bold text-slate-800 dark:text-white">{{ $dorm->name }}</td>
                                                    <td class="p-3">
                                                        <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-semibold {{ $dorm->gender === 'L' ? 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300' : 'bg-pink-100 text-pink-800 dark:bg-pink-950 dark:text-pink-300' }}">
                                                            {{ $dorm->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}
                                                        </span>
                                                    </td>
                                                    <td class="p-3">
                                                        <select wire:model="assigned_users.{{ $dorm->id }}" class="rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-2 py-1 text-xs focus:border-emerald-500 focus:ring-emerald-500">
                                                            <option value="">-- Pilih Penanggung Jawab --</option>
                                                            @foreach ($assignableUsers as $u)
                                                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="p-6 text-center text-slate-400">Belum ada asrama terpilih. Kembali ke Step 2.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- STEP 4: REVIEW & TIMELINE -->
                @if ($currentStep === 4)
                    <div class="space-y-6">
                        <div>
                            <h2 class="text-xl font-bold text-slate-800 dark:text-white">Timeline &amp; Review Sensus</h2>
                            <p class="text-sm text-slate-400">Atur batas waktu pengisian dan periksa kembali konfigurasi.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Timeline Picker -->
                            <div class="md:col-span-1 p-5 bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-slate-800 rounded-2xl space-y-4">
                                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Batas Waktu Pengisian</h3>
                                <div>
                                    <label for="deadline" class="block text-xs text-slate-500 mb-1">Tanggal Deadline <span class="text-rose-500">*</span></label>
                                    <input type="date" id="deadline" wire:model="deadline" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-3 py-2 text-xs focus:border-emerald-500 focus:ring-emerald-500">
                                    @error('deadline') <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <!-- Configuration Summary Review Card -->
                            <div class="md:col-span-2 p-5 bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-slate-800 rounded-2xl space-y-3 text-sm">
                                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4">Ringkasan Konfigurasi Sensus</h3>
                                
                                <div class="grid grid-cols-3 py-1.5 border-b border-slate-100 dark:border-slate-800/80">
                                    <span class="text-slate-400">Nama Kampanye</span>
                                    <span class="col-span-2 font-bold text-slate-800 dark:text-white">{{ $name }}</span>
                                </div>

                                <div class="grid grid-cols-3 py-1.5 border-b border-slate-100 dark:border-slate-800/80">
                                    <span class="text-slate-400">Periode Sensus</span>
                                    <span class="col-span-2 font-semibold text-slate-700 dark:text-slate-300">
                                        {{ $this->getMonthName($month) }} {{ $year }}
                                    </span>
                                </div>

                                <div class="grid grid-cols-3 py-1.5 border-b border-slate-100 dark:border-slate-800/80">
                                    <span class="text-slate-400">Template</span>
                                    <span class="col-span-2 font-semibold text-slate-700 dark:text-slate-300">
                                        {{ $templates->firstWhere('id', $template_id)->name ?? '-' }}
                                    </span>
                                </div>

                                <div class="grid grid-cols-3 py-1.5 border-b border-slate-100 dark:border-slate-800/80">
                                    <span class="text-slate-400">Target</span>
                                    <span class="col-span-2 font-semibold text-slate-700 dark:text-slate-300">
                                        @if ($target_scope === 'all')
                                            Semua Asrama ({{ $dormitories->count() }} asrama)
                                        @elseif ($target_scope === 'putra')
                                            Komplek Putra ({{ $dormitories->where('gender', 'L')->count() }} asrama)
                                        @elseif ($target_scope === 'putri')
                                            Komplek Putri ({{ $dormitories->where('gender', 'P')->count() }} asrama)
                                        @else
                                            Kustom ({{ count($target_dormitory_ids) }} asrama terpilih)
                                        @endif
                                    </span>
                                </div>

                                <div class="grid grid-cols-3 py-1.5">
                                    <span class="text-slate-400">Alur Kerja</span>
                                    <span class="col-span-2 font-semibold text-slate-700 dark:text-slate-300">
                                        @if ($workflow_mode === 'admin_only')
                                            Pusat / Admin Input Sendiri
                                        @elseif ($workflow_mode === 'distributed')
                                            Didistribusikan ke Ketua Komplek
                                        @elseif ($workflow_mode === 'excel')
                                            Unduh &amp; Unggah File Excel
                                        @else
                                            Hybrid (Web Input + Excel)
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Card Actions Footer -->
            <div class="px-6 py-4 bg-slate-50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <div>
                    @if ($currentStep > 1)
                        <button type="button" wire:click="prevStep" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold rounded-xl text-xs transition-all">
                            Sebelumnya
                        </button>
                    @endif
                </div>

                <div class="flex items-center gap-3">
                    @if ($currentStep < $totalSteps)
                        <button type="button" wire:click="nextStep" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-xl text-xs shadow-md shadow-emerald-600/10 hover:shadow-emerald-500/20 transition-all">
                            Selanjutnya
                        </button>
                    @else
                        <button type="button" wire:click="saveDraft" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-250 dark:bg-slate-800 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold rounded-xl text-xs transition-all">
                            Simpan Draft
                        </button>
                        <button type="button" wire:click="publish" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-xl text-xs shadow-md shadow-emerald-600/10 hover:shadow-emerald-500/20 transition-all">
                            Terbitkan &amp; Mulai Sensus
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
