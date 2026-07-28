<?php

namespace App\Livewire\Keuangan;

use Livewire\Component;
use App\Modules\Core\Models\Person;
use App\Modules\Kepengasuhan\Models\Dormitory;
use App\Modules\Madrasah\Models\MadrasahKelas;
use App\Modules\Keuangan\Models\BillingConfiguration;
use App\Modules\Keuangan\Models\Bill;
use App\Models\User;
use App\Traits\HasGenderScope;

class BillingConfigurationEdit extends Component
{
    use HasGenderScope;

    public string $configId = '';
    public string $newConfigName = '';
    public string $newConfigType = '';
    public float|int|string|null $newConfigAmount = null;
    public string $newConfigInterval = 'monthly';
    public string $newConfigDueDayType = 'fixed_day';
    public int|string|null $newConfigDueDayValue = 10;
    public ?string $newConfigDueDateSpecific = null;
    public array  $newConfigManagerRoles = [];
    public array  $newConfigManagerIds = [];
    public string $newConfigCoManagerSearchQuery = '';
    public string $newConfigTargetType = 'all';
    public array  $newConfigTargetFilters = []; // selected IDs (komplek/kelas/individual)
    public string $newConfigEffectiveFrom = '';
    public bool   $newConfigCanBeInstallment = false;
    public string $newConfigFilterDormitoryId = '';
    public string $newConfigFilterKelasId = '';
    public string $newConfigFilterSearch = '';
    public bool   $syncNewTargets = false;
    public array  $newConfigGenderTargets = [];
    public array  $newConfigResidenceTargets = ['mukim', 'laju'];

    public bool   $hasIssuedBills = false;

    public function mount(string $id): void
    {
        $config = BillingConfiguration::findOrFail($id);

        $this->configId = $config->id;
        $this->newConfigName = $config->label;
        $this->newConfigType = $config->type;
        $this->newConfigAmount = (float) $config->amount;
        $this->newConfigInterval = $config->interval;
        $this->newConfigDueDayType = $config->due_day_type ?? 'fixed_day';
        $this->newConfigDueDayValue = $config->due_day_value ?? 10;
        $this->newConfigDueDateSpecific = $config->due_date_specific ? $config->due_date_specific->format('Y-m-d') : null;
        $this->hasIssuedBills = Bill::where('billing_config_id', $config->id)->exists();

        // Safely parse manager_role whether stored as a JSON array or a plain string
        $rawRole = $config->getRawOriginal('manager_role');
        if ($rawRole) {
            $decoded = json_decode($rawRole, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $this->newConfigManagerRoles = $decoded;
            } else {
                $this->newConfigManagerRoles = [$rawRole];
            }
        } else {
            $this->newConfigManagerRoles = [];
        }

        $this->newConfigManagerIds = $config->manager_ids ?? [];
        $this->newConfigTargetType = $config->target_type;
        $this->newConfigEffectiveFrom = $config->effective_from ? $config->effective_from->toDateString() : '';
        $this->newConfigCanBeInstallment = (bool) $config->can_be_installment;
        $this->newConfigResidenceTargets = ['mukim', 'laju'];

        if (is_array($config->target_filters)) {
            if (isset($config->target_filters['genders'])) {
                $this->newConfigGenderTargets = (array) $config->target_filters['genders'];
            } elseif ($config->target_type === 'all') {
                $this->newConfigGenderTargets = array_values(array_intersect(['L', 'P'], (array)$config->target_filters));
            }

            if (isset($config->target_filters['residence'])) {
                $this->newConfigResidenceTargets = (array) $config->target_filters['residence'];
            }

            if (isset($config->target_filters['ids'])) {
                $this->newConfigTargetFilters = (array) $config->target_filters['ids'];
            } elseif ($config->target_type !== 'all') {
                $this->newConfigTargetFilters = (array) $config->target_filters;
            }
        } else {
            $this->newConfigTargetFilters = $config->target_filters ?? [];
            if ($this->genderScope() === 'L') {
                $this->newConfigGenderTargets = ['L'];
            } elseif ($this->genderScope() === 'P') {
                $this->newConfigGenderTargets = ['P'];
            } else {
                $this->newConfigGenderTargets = ['L', 'P'];
            }
        }
    }

    public function autoSelectMyRole(): void
    {
        $user = auth()->user();
        if ($user) {
            $roles = $user->roles->pluck('name')->toArray();
            $creatorRoles = array_values(array_diff($roles, ['super-admin', 'admin-data']));
            $this->newConfigManagerRoles = !empty($creatorRoles) ? $creatorRoles : $roles;
        }
    }

    public function clearManagerRoles(): void
    {
        $this->newConfigManagerRoles = [];
    }

    public function updatedNewConfigGenderTargets(): void
    {
        $this->newConfigTargetFilters = [];
    }

    public function selectAllTargetFilters(string $genderFilter = 'all'): void
    {
        $activeGenders = !empty($this->newConfigGenderTargets)
            ? $this->newConfigGenderTargets
            : ($this->genderScope() ? [$this->genderScope()] : ['L', 'P']);

        $gendersToSelect = $genderFilter !== 'all' ? [$genderFilter] : $activeGenders;
        if ($this->genderScope()) {
            $gendersToSelect = array_values(array_intersect($gendersToSelect, [$this->genderScope()]));
        }

        if ($this->newConfigTargetType === 'dormitory') {
            $dormIds = Dormitory::whereIn('gender', $gendersToSelect)->pluck('id')->toArray();
            $this->newConfigTargetFilters = array_values(array_unique(array_merge($this->newConfigTargetFilters, $dormIds)));
        } elseif ($this->newConfigTargetType === 'kelas') {
            $kelasIds = MadrasahKelas::where('is_active', true)
                ->where(function($q) use ($gendersToSelect) {
                    foreach ($gendersToSelect as $g) {
                        $keyword = $g === 'L' ? 'Putra' : 'Putri';
                        $oppositeGender = $g === 'L' ? 'P' : 'L';
                        $q->orWhere(function($sub) use ($keyword, $g, $oppositeGender) {
                            $sub->where('name', 'like', '%' . $keyword . '%')
                                ->orWhereHas('activeEnrollments.person', function($p) use ($g) {
                                    $p->where('gender', $g);
                                });
                        });
                    }
                })
                ->pluck('id')
                ->toArray();
            $this->newConfigTargetFilters = array_values(array_unique(array_merge($this->newConfigTargetFilters, $kelasIds)));
        }
    }

    public function clearAllTargetFilters(): void
    {
        $this->newConfigTargetFilters = [];
    }

    /**
     * Reset target filters whenever the target type changes,
     * so stale IDs (e.g. santri IDs) don't bleed into other type's counts.
     */
    public function updatedNewConfigTargetType(): void
    {
        $this->newConfigTargetFilters    = [];
        $this->newConfigFilterDormitoryId = '';
        $this->newConfigFilterKelasId    = '';
        $this->newConfigFilterSearch     = '';
    }

    public function addCoManager(string $userId): void
    {
        if (!in_array($userId, $this->newConfigManagerIds)) {
            $this->newConfigManagerIds[] = $userId;
        }
        $this->newConfigCoManagerSearchQuery = '';
    }

    public function removeCoManager(string $userId): void
    {
        $this->newConfigManagerIds = array_values(array_filter(
            $this->newConfigManagerIds,
            fn($val) => $val !== $userId
        ));
    }

    public function removeTargetFilter(string $id): void
    {
        $this->newConfigTargetFilters = array_values(array_filter(
            $this->newConfigTargetFilters,
            fn($val) => $val !== $id
        ));
    }

    public function getSelectedIndividualSantriProperty(): \Illuminate\Support\Collection
    {
        if ($this->newConfigTargetType !== 'individual' || empty($this->newConfigTargetFilters)) {
            return collect();
        }
        return Person::whereIn('id', $this->newConfigTargetFilters)
            ->orderBy('name')
            ->get(['id', 'name', 'gender']);
    }

    public function getSelectedCoManagersProperty(): \Illuminate\Support\Collection
    {
        if (empty($this->newConfigManagerIds)) {
            return collect();
        }
        return User::whereIn('id', $this->newConfigManagerIds)->get();
    }

    public function toggleAllIndividualSantri(bool $select): void
    {
        $filteredIds = Person::whereHas('activeRoles', function ($q) {
            $q->where('role_type', 'santri')->where('enrollment_status', 'aktif');
        })
        ->when($this->genderScope(), fn($q, $g) => $q->where('gender', $g))
        ->when($this->newConfigFilterDormitoryId, function ($q, $dormId) {
            $q->whereHas('roomAssignments', function ($rq) use ($dormId) {
                $rq->where('is_active', true)
                  ->whereHas('room', fn($rq2) => $rq2->where('dormitory_id', $dormId));
            });
        })
        ->when($this->newConfigFilterKelasId, function ($q, $kelasId) {
            $q->whereHas('madrasahEnrollments', function ($eq) use ($kelasId) {
                $eq->where('is_active', true)->where('kelas_id', $kelasId);
            });
        })
        ->when($this->newConfigFilterSearch, function ($q, $search) {
            $q->where('name', 'like', '%' . $search . '%');
        })
        ->pluck('id')
        ->toArray();

        if ($select) {
            $this->newConfigTargetFilters = array_unique(array_merge($this->newConfigTargetFilters, $filteredIds));
        } else {
            $this->newConfigTargetFilters = array_values(array_diff($this->newConfigTargetFilters, $filteredIds));
        }
    }

    public function updateConfig(\App\Modules\Keuangan\Services\BillingService $billingService)
    {
        $this->validate([
            'newConfigName' => 'required|string|max:255',
            'newConfigAmount' => 'required|numeric|min:0',
            'newConfigDueDayType' => 'required|string|in:fixed_day,fixed_date,days_after,none',
            'newConfigDueDayValue' => 'nullable|numeric|min:1|max:31',
            'newConfigDueDateSpecific' => 'nullable|required_if:newConfigDueDayType,fixed_date|date',
            'newConfigEffectiveFrom' => 'required|date',
            'newConfigGenderTargets' => 'required|array|min:1',
            'newConfigResidenceTargets' => 'required|array|min:1',
            'newConfigTargetType' => 'required|string',
            'newConfigCanBeInstallment' => 'required|boolean',
            'newConfigTargetFilters' => $this->newConfigTargetType !== 'all' ? 'required|array|min:1' : 'nullable|array',
        ], [
            'newConfigGenderTargets.required' => 'Anda harus memilih minimal satu target gender santri (Putra atau Putri).',
            'newConfigGenderTargets.min' => 'Anda harus memilih minimal satu target gender santri (Putra atau Putri).',
            'newConfigResidenceTargets.required' => 'Anda harus memilih minimal satu status residensi (Mukim atau Laju).',
            'newConfigResidenceTargets.min' => 'Anda harus memilih minimal satu status residensi (Mukim atau Laju).',
            'newConfigTargetFilters.required' => 'Anda harus memilih minimal satu komplek, kelas, atau santri target.',
            'newConfigTargetFilters.min' => 'Anda harus memilih minimal satu komplek, kelas, atau santri target.',
        ]);

        $genderTargets = array_values(array_intersect(['L', 'P'], $this->newConfigGenderTargets));
        $residenceTargets = array_values(array_intersect(['mukim', 'laju'], $this->newConfigResidenceTargets));

        $targetFilters = [
            'genders' => $genderTargets,
            'residence' => $residenceTargets,
        ];
        if ($this->newConfigTargetType !== 'all') {
            $targetFilters['ids'] = $this->newConfigTargetFilters;
        }

        $config = BillingConfiguration::findOrFail($this->configId);
        $config->update([
            'label' => $this->newConfigName,
            'amount' => $this->newConfigAmount,
            'interval' => $this->newConfigInterval,
            'effective_from' => $this->newConfigEffectiveFrom,
            'due_day_type' => $this->newConfigDueDayType,
            'due_day_value' => $this->newConfigDueDayValue ? (int)$this->newConfigDueDayValue : null,
            'due_date_specific' => $this->newConfigDueDayType === 'fixed_date' ? $this->newConfigDueDateSpecific : null,
            'manager_role' => !empty($this->newConfigManagerRoles) ? json_encode($this->newConfigManagerRoles) : null,
            'manager_ids' => !empty($this->newConfigManagerIds) ? $this->newConfigManagerIds : null,
            'target_type' => $this->newConfigTargetType,
            'target_filters' => !empty($targetFilters) ? $targetFilters : null,
            'can_be_installment' => $this->newConfigCanBeInstallment,
        ]);

        $syncMsg = '';
        if ($this->syncNewTargets) {
            $month = (int) now()->format('m');
            $year = (int) now()->format('Y');
            $syncResult = $billingService->generateBillsFromConfig($config->id, $month, $year, auth()->id() ?: User::first()?->id);
            $syncMsg = " Serta berhasil mensinkronkan {$syncResult['generated']} tagihan baru untuk periode berjalan ({$month}/{$year}).";
        }

        session()->flash('message', 'Konfigurasi tarif berhasil diperbarui.' . $syncMsg);

        return redirect()->route('keuangan.billing', ['tab' => 'rates']);
    }

    public function render()
    {
        $coManagerSearch = [];
        if (strlen($this->newConfigCoManagerSearchQuery) >= 3) {
            $coManagerSearch = User::where('name', 'like', '%' . $this->newConfigCoManagerSearchQuery . '%')
                ->limit(5)
                ->get();
        }

        $activeGenders = !empty($this->newConfigGenderTargets) ? $this->newConfigGenderTargets : ($this->genderScope() ? [$this->genderScope()] : ['L', 'P']);

        $individualSantriOptions = [];
        if ($this->newConfigTargetType === 'individual') {
            $individualSantriOptions = Person::whereHas('activeRoles', function ($q) {
                $q->where('role_type', 'santri')->where('enrollment_status', 'aktif');
            })
            ->whereIn('gender', $activeGenders)
            ->when($this->newConfigFilterDormitoryId, function ($q, $dormId) {
                $q->whereHas('roomAssignments', function ($rq) use ($dormId) {
                    $rq->where('is_active', true)
                      ->whereHas('room', fn($rq2) => $rq2->where('dormitory_id', $dormId));
                });
            })
            ->when($this->newConfigFilterKelasId, function ($q, $kelasId) {
                $q->whereHas('madrasahEnrollments', function ($eq) use ($kelasId) {
                    $eq->where('is_active', true)->where('kelas_id', $kelasId);
                });
            })
            ->when($this->newConfigFilterSearch, function ($q, $search) {
                $q->where('name', 'like', '%' . $search . '%');
            })
            ->orderBy('name')
            ->limit(200)
            ->get();
        }

        return view('livewire.keuangan.billing-configuration-edit', [
            'coManagerSearchResults' => $coManagerSearch,
            'individualSantriOptions' => $individualSantriOptions,
            'dormitories' => Dormitory::whereIn('gender', $activeGenders)->get(),
            'kelasList' => MadrasahKelas::where('is_active', true)
                ->where(function($q) use ($activeGenders) {
                    foreach ($activeGenders as $g) {
                        $keyword = $g === 'L' ? 'Putra' : 'Putri';
                        $oppositeGender = $g === 'L' ? 'P' : 'L';
                        $q->orWhere(function($sub) use ($keyword, $g, $oppositeGender) {
                            $sub->where('name', 'like', '%' . $keyword . '%')
                                ->orWhereHas('activeEnrollments.person', function($p) use ($g) {
                                    $p->where('gender', $g);
                                });
                        });
                    }
                })
                ->orderBy('jenjang')
                ->orderBy('name')
                ->get(),
            'systemRoles' => \Spatie\Permission\Models\Role::orderBy('name')->get(),
        ])->layout('layouts.app');
    }
}
