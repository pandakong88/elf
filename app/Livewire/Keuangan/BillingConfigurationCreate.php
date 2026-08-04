<?php

namespace App\Livewire\Keuangan;

use Livewire\Component;
use App\Modules\Core\Models\Person;
use App\Modules\Kepengasuhan\Models\Dormitory;
use App\Modules\Madrasah\Models\MadrasahKelas;
use App\Modules\Keuangan\Models\BillingConfiguration;
use App\Models\User;
use Illuminate\Support\Str;

use App\Traits\HasGenderScope;

class BillingConfigurationCreate extends Component
{
    use HasGenderScope;
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
    public array  $newConfigGenderTargets = [];
    public array  $newConfigResidenceTargets = ['mukim', 'laju'];
    public string $newConfigFilterDormitoryId = '';
    public string $newConfigFilterKelasId = '';
    public string $newConfigFilterSearch = '';

    public function mount(?string $copy_from = null): void
    {
        $this->newConfigEffectiveFrom = now()->toDateString();
        $this->newConfigResidenceTargets = ['mukim', 'laju'];
        
        if ($this->genderScope() === 'L') {
            $this->newConfigGenderTargets = ['L'];
        } elseif ($this->genderScope() === 'P') {
            $this->newConfigGenderTargets = ['P'];
        } else {
            $this->newConfigGenderTargets = ['L', 'P'];
        }

        $this->autoSelectMyRole();

        $copyId = $copy_from ?: request()->query('copy_from');
        if ($copyId) {
            $source = BillingConfiguration::find($copyId);
            if ($source) {
                $labelBase = $source->label ?: str_replace('_', ' ', $source->type);
                $this->newConfigName = $labelBase . ' (Salinan)';
                $this->newConfigType = $source->type;
                $this->newConfigAmount = (float)$source->amount;
                $this->newConfigInterval = $source->interval ?: 'monthly';
                $this->newConfigDueDayType = $source->due_day_type ?: 'fixed_day';
                $this->newConfigDueDayValue = $source->due_day_value ?: 10;
                $this->newConfigDueDateSpecific = $source->due_date_specific?->format('Y-m-d');
                $this->newConfigTargetType = $source->target_type ?: 'all';
                $this->newConfigCanBeInstallment = (bool) $source->can_be_installment;

                $roles = is_array($source->manager_role) ? $source->manager_role : (json_decode($source->manager_role, true) ?: []);
                if (!empty($roles)) {
                    $this->newConfigManagerRoles = $roles;
                }

                $filters = is_array($source->target_filters) ? $source->target_filters : (json_decode($source->target_filters, true) ?: []);
                if (!empty($filters)) {
                    $this->newConfigTargetFilters = $filters['ids'] ?? [];
                    if (!empty($filters['genders'])) {
                        $this->newConfigGenderTargets = $filters['genders'];
                    }
                    if (!empty($filters['residence'])) {
                        $this->newConfigResidenceTargets = $filters['residence'];
                    }
                }
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

    public function createConfig()
    {
        $this->validate([
            'newConfigName' => 'required|string|max:255',
            'newConfigType' => 'required|string',
            'newConfigAmount' => 'required|numeric|min:0',
            'newConfigInterval' => 'required|string',
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

        BillingConfiguration::create([
            'id' => Str::uuid()->toString(),
            'type' => $this->newConfigType,
            'label' => $this->newConfigName,
            'amount' => $this->newConfigAmount,
            'effective_from' => $this->newConfigEffectiveFrom,
            'interval' => $this->newConfigInterval,
            'due_day_type' => $this->newConfigDueDayType,
            'due_day_value' => $this->newConfigDueDayValue ? (int)$this->newConfigDueDayValue : null,
            'due_date_specific' => $this->newConfigDueDayType === 'fixed_date' ? $this->newConfigDueDateSpecific : null,
            'manager_role' => !empty($this->newConfigManagerRoles) ? json_encode($this->newConfigManagerRoles) : null,
            'manager_ids' => !empty($this->newConfigManagerIds) ? $this->newConfigManagerIds : null,
            'target_type' => $this->newConfigTargetType,
            'target_filters' => !empty($targetFilters) ? $targetFilters : null,
            'can_be_installment' => $this->newConfigCanBeInstallment,
            'is_active' => true,
            'created_by' => auth()->id() ?: User::first()?->id,
        ]);

        session()->flash('message', 'Konfigurasi tarif dinamis baru berhasil ditambahkan.');

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

        return view('livewire.keuangan.billing-configuration-create', [
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
