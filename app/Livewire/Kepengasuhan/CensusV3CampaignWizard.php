<?php

namespace App\Livewire\Kepengasuhan;

use Livewire\Component;
use App\Livewire\Concerns\SendsToast;
use App\Modules\Kepengasuhan\Models\CensusTemplate;
use App\Modules\Kepengasuhan\Models\CensusV3Campaign;
use App\Modules\Kepengasuhan\Models\Dormitory;
use App\Models\User;
use App\Modules\Kepengasuhan\Services\CensusV3Service;
use App\Modules\Kepengasuhan\Services\CensusV3AccessService;
use Carbon\Carbon;

class CensusV3CampaignWizard extends Component
{
    use SendsToast;

    // Step tracking
    public int $currentStep = 1;
    public int $totalSteps = 4;

    // Step 1: Info & Template
    public string $census_type = 'formal'; // formal, mutasi, fasilitas
    public string $name = '';
    public string $description = '';
    public int $month;
    public int $year;
    public string $template_id = '';

    // Step 2: Target Scope
    public string $target_scope = 'all'; // all, putra, putri, custom_dormitories
    public array $target_dormitory_ids = []; // selected dormitory IDs for custom_dormitories

    // Step 3: Workflow & Assignments
    public string $workflow_mode = 'distributed'; // admin_only, distributed, excel, hybrid
    public bool $allow_excel = false;
    public bool $allow_direct_input = true;
    public array $assigned_users = []; // [dormitory_id => [user_id, ...]]

    // Step 4: Timeline & Review
    public ?string $deadline = null;

    protected CensusV3Service $censusService;
    protected CensusV3AccessService $accessService;

    public function boot(CensusV3Service $censusService, CensusV3AccessService $accessService): void
    {
        $this->censusService = $censusService;
        $this->accessService = $accessService;
    }

    public function mount(): void
    {
        $user = auth()->user();
        if (!$user || !$user->can('manage-sensus-v3')) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk membuat kampanye sensus.');
        }

        $now = Carbon::now();
        $this->month = $now->month;
        $this->year = $now->year;

        $type = request()->query('type');
        if (in_array($type, ['formal', 'mutasi', 'fasilitas'])) {
            $this->census_type = $type;
        }

        if ($this->census_type === 'mutasi') {
            $this->name = "Sensus Santri (Mutasi) - " . $this->getMonthName($this->month) . " " . $this->year;
        } elseif ($this->census_type === 'fasilitas') {
            $this->name = "Sensus Fasilitas & Lemari - " . $this->getMonthName($this->month) . " " . $this->year;
        } else {
            $this->name = "Sensus Bulanan - " . $this->getMonthName($this->month) . " " . $this->year;
        }

        // Kunci target_scope berdasarkan gender sekretaris jika ada
        $userScope = $this->accessService->genderScope($user);
        if ($userScope === 'L') {
            $this->target_scope = 'putra';
        } elseif ($userScope === 'P') {
            $this->target_scope = 'putri';
        } else {
            $this->target_scope = 'all';
        }

        // Set default template if exists
        $defaultTemplate = CensusTemplate::where('is_default', true)->where('is_archived', false)->first();
        if ($defaultTemplate) {
            $this->template_id = $defaultTemplate->id;
        }

        // Initialize deadline to end of current month
        $this->deadline = $now->endOfMonth()->toDateString();
    }

    public function getTemplatesProperty()
    {
        return CensusTemplate::where('is_archived', false)->orderBy('name')->get();
    }

    public function getDormitoriesProperty()
    {
        $query = Dormitory::where('is_active', true);
        $userScope = $this->accessService->genderScope(auth()->user());
        if ($userScope) {
            $query->where('gender', $userScope);
        }
        return $query->orderBy('name')->get();
    }

    public function getTargetedDormitoriesProperty()
    {
        $query = Dormitory::where('is_active', true);
        $userScope = $this->accessService->genderScope(auth()->user());

        if ($userScope) {
            $query->where('gender', $userScope);
        }

        if ($this->target_scope === 'putra') {
            $query->where('gender', 'L');
        } elseif ($this->target_scope === 'putri') {
            $query->where('gender', 'P');
        } elseif ($this->target_scope === 'custom_dormitories') {
            $query->whereIn('id', $this->target_dormitory_ids);
        }

        return $query->orderBy('name')->get();
    }

    public function getAssignableUsersProperty()
    {
        $query = User::where('is_active', true)
            ->whereHas('roles', function ($q) {
                $q->whereIn('name', [
                    'musyrif',
                    'sekretaris-putra',
                    'sekretaris-putri',
                    'manajemen',
                    'super-admin'
                ]);
            });

        $userScope = $this->accessService->genderScope(auth()->user());
        if ($userScope) {
            $query->where(function ($sub) use ($userScope) {
                $sub->whereHas('person', fn($pq) => $pq->where('gender', $userScope))
                    ->orWhereHas('roles', fn($rq) => $rq->whereIn('name', ['super-admin', 'manajemen']));
            });
        }

        return $query->orderBy('name')->get();
    }

    public function updatedMonth($value): void
    {
        $this->updateDefaultName();
    }

    public function updatedYear($value): void
    {
        $this->updateDefaultName();
    }

    private function updateDefaultName(): void
    {
        $this->name = "Sensus Bulanan - " . $this->getMonthName($this->month) . " " . $this->year;
    }

    private function getMonthName(int $m): string
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        return $months[$m] ?? (string)$m;
    }

    public function nextStep(): void
    {
        $this->validateStep();
        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
        }
    }

    public function prevStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    private function validateStep(): void
    {
        $userScope = $this->accessService->genderScope(auth()->user());

        if ($this->currentStep === 1) {
            $this->validate([
                'name'        => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'month'       => 'required|integer|between:1,12',
                'year'        => 'required|integer|min:2020|max:2100',
                'template_id' => 'required|uuid|exists:census_templates,id',
            ]);
        } elseif ($this->currentStep === 2) {
            if ($userScope === 'L') {
                $this->validate([
                    'target_scope' => 'required|in:putra,custom_dormitories',
                ], [
                    'target_scope.in' => 'Sebagai Sekretaris Putra, Anda hanya dapat memilih komplek putra atau asrama kustom putra.',
                ]);
            } elseif ($userScope === 'P') {
                $this->validate([
                    'target_scope' => 'required|in:putri,custom_dormitories',
                ], [
                    'target_scope.in' => 'Sebagai Sekretaris Putri, Anda hanya dapat memilih komplek putri atau asrama kustom putri.',
                ]);
            } else {
                $this->validate([
                    'target_scope' => 'required|in:all,putra,putri,custom_dormitories',
                ]);
            }

            if ($this->target_scope === 'custom_dormitories') {
                $this->validate([
                    'target_dormitory_ids' => 'required|array|min:1',
                ], [
                    'target_dormitory_ids.required' => 'Pilih minimal satu asrama.',
                ]);

                // Pastikan asrama yang dipilih sesuai scope gender
                if ($userScope) {
                    $invalidCount = Dormitory::whereIn('id', $this->target_dormitory_ids)
                        ->where('gender', '!=', $userScope)
                        ->count();
                    if ($invalidCount > 0) {
                        $this->addError('target_dormitory_ids', 'Asrama yang dipilih harus sesuai dengan gender binaan Anda.');
                        throw new \Illuminate\Validation\ValidationException($this->validator);
                    }
                }
            }
        } elseif ($this->currentStep === 3) {
            $this->validate([
                'workflow_mode'      => 'required|in:admin_only,distributed,excel,hybrid',
                'allow_excel'        => 'boolean',
                'allow_direct_input' => 'boolean',
            ]);

            // Ensure direct input or excel is allowed
            if (!$this->allow_excel && !$this->allow_direct_input) {
                $this->addError('allow_direct_input', 'Minimal satu metode input (Form Web / Excel) harus diaktifkan.');
                throw new \Illuminate\Validation\ValidationException($this->validator);
            }
        }
    }

    public function saveDraft(): void
    {
        $this->saveCampaign('draft');
    }

    public function publish(): void
    {
        $this->saveCampaign('active');
    }

    private function saveCampaign(string $status): void
    {
        $this->currentStep = 4; // Ensure we are on review validation
        $this->validate([
            'deadline' => 'required|date|after_or_equal:today',
        ]);

        try {
            $campaignData = [
                'name'                 => $this->name,
                'description'          => $this->description,
                'census_type'          => $this->census_type,
                'template_id'          => $this->template_id ?: null,
                'month'                => $this->month,
                'year'                 => $this->year,
                'target_scope'         => $this->target_scope,
                'target_dormitory_ids' => $this->target_dormitory_ids,
                'workflow_mode'        => $this->workflow_mode,
                'allow_excel'          => $this->allow_excel,
                'allow_direct_input'   => $this->allow_direct_input,
                'deadline'             => $this->deadline,
                'assigned_users'       => $this->assigned_users,
            ];

            $campaign = $this->censusService->createCampaign($campaignData, auth()->id());

            if ($status === 'active') {
                $this->censusService->activateCampaign($campaign->id);
                $this->toastSuccess('Kampanye sensus berhasil diterbitkan dan dimulai.');
            } else {
                $this->toastSuccess('Draft kampanye sensus berhasil disimpan.');
            }

            $this->redirect(route('sensus.campaigns'));
        } catch (\Exception $e) {
            $this->addError('deadline', 'Gagal menyimpan kampanye: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.kepengasuhan.census-v3-campaign-wizard', [
            'templates'           => $this->templates,
            'dormitories'         => $this->dormitories,
            'targetedDormitories' => $this->targetedDormitories,
            'assignableUsers'     => $this->assignableUsers,
            'userGenderScope'     => $this->accessService->genderScope(auth()->user()),
        ])->layout('layouts.app', ['title' => 'Buat Kampanye Sensus']);
    }
}
