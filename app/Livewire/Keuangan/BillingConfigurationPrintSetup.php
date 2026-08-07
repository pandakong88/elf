<?php

namespace App\Livewire\Keuangan;

use Livewire\Component;
use App\Modules\Core\Models\Person;
use App\Modules\Kepengasuhan\Models\Dormitory;
use App\Modules\Keuangan\Models\BillingConfiguration;
use App\Modules\Keuangan\Models\Bill;
use App\Modules\Madrasah\Models\MadrasahKelas;
use App\Modules\Madrasah\Models\MadrasahEnrollment;
use App\Traits\HasGenderScope;

class BillingConfigurationPrintSetup extends Component
{
    use HasGenderScope;

    public string $configId = '';
    public array $selectedDormitoryIds = [];
    public array $selectedKelasIds = [];
    public int $selectedMonth;
    public int $selectedYear;
    public int $selectedSemester = 1;
    public string $paperSize = 'a4';
    public bool $pageBreakPerRoom = false;

    public function mount(string $id): void
    {
        $config = BillingConfiguration::findOrFail($id);
        $this->configId = $config->id;

        $this->selectedMonth = (int) now()->format('m');
        $this->selectedYear = (int) now()->format('Y');
        $this->selectedSemester = now()->month <= 6 ? 1 : 2;

        if ($config->target_type === 'kelas') {
            $classesQuery = MadrasahKelas::where('is_active', true);
            if (!empty($config->target_filters)) {
                $classesQuery->whereIn('id', collect($config->target_filters)->flatten()->toArray());
            }
            $classes = $classesQuery->orderBy('name')->get();
            $this->selectedKelasIds = $classes->pluck('id')->toArray();
        } else {
            $dormitoriesQuery = Dormitory::query();
            if ($config->target_type === 'dormitory' && !empty($config->target_filters)) {
                $dormitoriesQuery->whereIn('id', collect($config->target_filters)->flatten()->toArray());
            }
            $dormitories = $dormitoriesQuery->when($this->genderScope(), fn($q, $g) => $q->where('gender', $g))->get();
            $this->selectedDormitoryIds = $dormitories->pluck('id')->toArray();
        }
    }

    public function selectAll(string $genderFilter = 'all'): void
    {
        $config = BillingConfiguration::findOrFail($this->configId);

        if ($config->target_type === 'kelas') {
            $kelasQuery = MadrasahKelas::where('is_active', true);
            if (!empty($config->target_filters)) {
                $kelasQuery->whereIn('id', collect($config->target_filters)->flatten()->toArray());
            }

            $g = $this->genderScope() ?: ($genderFilter !== 'all' ? $genderFilter : null);
            if ($g) {
                $keyword = $g === 'L' ? 'Putra' : 'Putri';
                $oppositeGender = $g === 'L' ? 'P' : 'L';
                $kelasQuery->where(function($sub) use ($keyword, $g) {
                    $sub->where('name', 'like', '%' . $keyword . '%')
                        ->orWhereHas('activeEnrollments.person', function($p) use ($g) {
                            $p->where('gender', $g);
                        });
                })
                ->whereDoesntHave('activeEnrollments.person', function($p) use ($oppositeGender) {
                    $p->where('gender', $oppositeGender);
                });
            }

            $this->selectedKelasIds = $kelasQuery->pluck('id')->toArray();
        } else {
            $dormQuery = Dormitory::query();
            if ($config->target_type === 'dormitory' && !empty($config->target_filters)) {
                $dormQuery->whereIn('id', collect($config->target_filters)->flatten()->toArray());
            }

            $g = $this->genderScope() ?: ($genderFilter !== 'all' ? $genderFilter : null);
            if ($g) {
                $dormQuery->where('gender', $g);
            }

            $this->selectedDormitoryIds = $dormQuery->pluck('id')->toArray();
        }
    }

    public function clearAllSelection(): void
    {
        $this->selectedKelasIds = [];
        $this->selectedDormitoryIds = [];
    }

    public function getPreviewDataProperty(): array
    {
        $config = BillingConfiguration::findOrFail($this->configId);
        $layoutType = $config->interval;

        // 1. Get santri list based on target_type
        if ($config->target_type === 'kelas') {
            if (empty($this->selectedKelasIds)) {
                return [];
            }

            $santriIds = MadrasahEnrollment::whereIn('kelas_id', $this->selectedKelasIds)
                ->where('is_active', true)
                ->pluck('person_id');

            if ($santriIds->isEmpty()) {
                return [];
            }

            $santriList = Person::whereIn('id', $santriIds)
                ->when($this->genderScope(), fn($q, $g) => $q->where('gender', $g))
                ->orderBy('name')
                ->limit(10)
                ->get();
        } else {
            if (empty($this->selectedDormitoryIds)) {
                return [];
            }

            $query = Person::select('persons.*', 'rooms.name as room_name', 'dormitories.name as dormitory_name')
                ->whereHas('activeRoles', fn($q) =>
                    $q->where('role_type', 'santri')->where('enrollment_status', 'aktif')
                )
                ->join('room_assignments', 'room_assignments.person_id', '=', 'persons.id')
                ->join('rooms', 'rooms.id', '=', 'room_assignments.room_id')
                ->join('dormitories', 'dormitories.id', '=', 'rooms.dormitory_id')
                ->whereIn('rooms.dormitory_id', $this->selectedDormitoryIds)
                ->where('room_assignments.is_active', true)
                ->when($this->genderScope(), fn($q, $g) => $q->where('persons.gender', $g))
                ->orderBy('rooms.name')
                ->orderBy('persons.name')
                ->limit(10);

            $santriList = $query->get();
        }

        $exceptions = \App\Modules\Keuangan\Models\BillingException::where('billing_config_id', $config->id)->get()->keyBy('person_id');

        $getExpectedTariff = function ($santriId) use ($config, $exceptions) {
            $exception = $exceptions->get($santriId);
            if (!$exception) {
                return [
                    'amount' => (float)$config->amount,
                    'note'   => null,
                ];
            }

            if ($exception->exception_type === 'waived') {
                $amount = 0.00;
                $note   = $exception->notes ?: 'Bebas (100%)';
            } elseif ($exception->exception_type === 'discount') {
                $amount = max(0.00, (float)$config->amount - (float)$exception->amount);
                $note   = $exception->notes ?: 'Diskon Rp ' . number_format($exception->amount, 0, ',', '.');
            } elseif ($exception->exception_type === 'custom_rate') {
                $amount = (float)$exception->amount;
                $note   = $exception->notes ?: 'Tarif Khusus';
            } else {
                $amount = (float)$config->amount;
                $note   = null;
            }

            return [
                'amount' => $amount,
                'note'   => $note,
            ];
        };

        // 2. Prepare headers and bills mapping based on layoutType
        if ($config->can_be_installment) {
            // Installment-based layout
            $maxTerms = Bill::where('billing_config_id', $config->id)
                ->whereNotNull('parent_bill_id')
                ->whereIn('person_id', $santriList->pluck('id'))
                ->get()
                ->groupBy('parent_bill_id')
                ->map(fn($group) => $group->count())
                ->max() ?: 3;

            $terms = range(1, $maxTerms);
            $headers = array_map(fn($t) => "Termin {$t}", $terms);

            $gridData = $santriList->map(function ($santri) use ($config, $terms, $getExpectedTariff) {
                $parentBill = Bill::where('person_id', $santri->id)
                    ->where('billing_config_id', $config->id)
                    ->whereNull('parent_bill_id')
                    ->first();

                $childBills = $parentBill 
                    ? Bill::where('parent_bill_id', $parentBill->id)->orderBy('due_date')->get()
                    : collect();

                $bills = [];
                foreach ($terms as $t) {
                    $bills[] = $childBills->get($t - 1);
                }

                $tariffInfo = $getExpectedTariff($santri->id);

                return [
                    'person' => $santri,
                    'room_name' => $santri->room_name ?? null,
                    'dormitory_name' => $santri->dormitory_name ?? null,
                    'bills' => $bills,
                    'expectedAmount' => $tariffInfo['amount'],
                    'exceptionNote' => $tariffInfo['note'],
                ];
            });

            return [
                'type' => 'installment',
                'headers' => $headers,
                'gridData' => $gridData,
            ];
        } elseif (in_array($layoutType, ['caturwulan', '3x_yearly'])) {
            // Caturwulan layout (3 columns)
            $periods = [
                1 => "Caturwulan 1 (Jan–Apr)",
                2 => "Caturwulan 2 (Mei–Agt)",
                3 => "Caturwulan 3 (Sep–Des)",
            ];

            $gridData = $santriList->map(function ($santri) use ($periods, $config, $getExpectedTariff) {
                $bills = [];
                foreach (array_keys($periods) as $pNum) {
                    $bill = Bill::where('person_id', $santri->id)
                        ->where('billing_config_id', $config->id)
                        ->where('period_month', (int)$pNum)
                        ->where('period_year', (int)$this->selectedYear)
                        ->first();
                    $bills[] = $bill;
                }

                $tariffInfo = $getExpectedTariff($santri->id);

                return [
                    'person' => $santri,
                    'room_name' => $santri->room_name ?? null,
                    'dormitory_name' => $santri->dormitory_name ?? null,
                    'bills' => $bills,
                    'expectedAmount' => $tariffInfo['amount'],
                    'exceptionNote' => $tariffInfo['note'],
                ];
            });

            return [
                'type' => 'caturwulan',
                'headers' => array_values($periods),
                'gridData' => $gridData,
            ];
        } elseif (in_array($layoutType, ['triwulan', '4x_yearly'])) {
            // Triwulan layout (4 columns)
            $periods = [
                1 => "Triwulan 1 (Jan–Mar)",
                2 => "Triwulan 2 (Apr–Jun)",
                3 => "Triwulan 3 (Jul–Sep)",
                4 => "Triwulan 4 (Okt–Des)",
            ];

            $gridData = $santriList->map(function ($santri) use ($periods, $config, $getExpectedTariff) {
                $bills = [];
                foreach (array_keys($periods) as $pNum) {
                    $bill = Bill::where('person_id', $santri->id)
                        ->where('billing_config_id', $config->id)
                        ->where('period_month', (int)$pNum)
                        ->where('period_year', (int)$this->selectedYear)
                        ->first();
                    $bills[] = $bill;
                }

                $tariffInfo = $getExpectedTariff($santri->id);

                return [
                    'person' => $santri,
                    'room_name' => $santri->room_name ?? null,
                    'dormitory_name' => $santri->dormitory_name ?? null,
                    'bills' => $bills,
                    'expectedAmount' => $tariffInfo['amount'],
                    'exceptionNote' => $tariffInfo['note'],
                ];
            });

            return [
                'type' => 'triwulan',
                'headers' => array_values($periods),
                'gridData' => $gridData,
            ];
        } elseif ($layoutType === 'semester') {
            // Semester layout (2 columns)
            $periods = [];
            $prevSem  = $this->selectedSemester === 1 ? 2 : 1;
            $prevYear = $this->selectedSemester === 1 ? $this->selectedYear - 1 : $this->selectedYear;
            $periods["{$prevSem}-{$prevYear}"] = "Sem {$prevSem} / {$prevYear}";
            $periods["{$this->selectedSemester}-{$this->selectedYear}"] = "Sem {$this->selectedSemester} / {$this->selectedYear}";

            $gridData = $santriList->map(function ($santri) use ($periods, $config, $getExpectedTariff) {
                $bills = [];
                foreach ($periods as $periodKey => $periodLabel) {
                    [$sem, $yr] = explode('-', $periodKey);
                    $bill = Bill::where('person_id', $santri->id)
                        ->where('billing_config_id', $config->id)
                        ->where('period_month', (int)$sem)
                        ->where('period_year', (int)$yr)
                        ->first();
                    $bills[] = $bill;
                }

                $tariffInfo = $getExpectedTariff($santri->id);

                return [
                    'person' => $santri,
                    'room_name' => $santri->room_name ?? null,
                    'dormitory_name' => $santri->dormitory_name ?? null,
                    'bills' => $bills,
                    'expectedAmount' => $tariffInfo['amount'],
                    'exceptionNote' => $tariffInfo['note'],
                ];
            });

            return [
                'type' => 'semester',
                'headers' => array_values($periods),
                'gridData' => $gridData,
            ];
        } elseif ($layoutType === 'monthly') {
            // Monthly layout (12 columns)
            $months = [];
            for ($m = 1; $m <= 12; $m++) {
                $date = now()->setDate($this->selectedYear, $m, 1);
                $key  = $m . '-' . $this->selectedYear;
                $months[$key] = $date->locale('id')->translatedFormat('M');
            }

            $gridData = $santriList->map(function ($santri) use ($months, $config, $getExpectedTariff) {
                $bills = [];
                foreach ($months as $periodKey => $periodLabel) {
                    [$m, $y] = explode('-', $periodKey);
                    $bill = Bill::where('person_id', $santri->id)
                        ->where('billing_config_id', $config->id)
                        ->where('period_month', (int)$m)
                        ->where('period_year', (int)$y)
                        ->first();
                    $bills[] = $bill;
                }

                $tariffInfo = $getExpectedTariff($santri->id);

                return [
                    'person' => $santri,
                    'room_name' => $santri->room_name ?? null,
                    'dormitory_name' => $santri->dormitory_name ?? null,
                    'bills' => $bills,
                    'expectedAmount' => $tariffInfo['amount'],
                    'exceptionNote' => $tariffInfo['note'],
                ];
            });

            return [
                'type' => 'monthly',
                'headers' => array_values($months),
                'gridData' => $gridData,
            ];
        } else {
            // Yearly / once / insidental (single column for the bill)
            $gridData = $santriList->map(function ($santri) use ($config, $getExpectedTariff) {
                $bill = Bill::where('person_id', $santri->id)
                    ->where('billing_config_id', $config->id)
                    ->first();

                $tariffInfo = $getExpectedTariff($santri->id);

                return [
                    'person' => $santri,
                    'room_name' => $santri->room_name ?? null,
                    'dormitory_name' => $santri->dormitory_name ?? null,
                    'bills' => [$bill],
                    'expectedAmount' => $tariffInfo['amount'],
                    'exceptionNote' => $tariffInfo['note'],
                ];
            });

            return [
                'type' => 'single',
                'headers' => ['Status Bayar'],
                'gridData' => $gridData,
            ];
        }
    }

    public function render()
    {
        $config = BillingConfiguration::findOrFail($this->configId);
        
        $dormitoriesQuery = Dormitory::query();
        if ($config->target_type === 'dormitory' && !empty($config->target_filters)) {
            $dormitoriesQuery->whereIn('id', collect($config->target_filters)->flatten()->toArray());
        }
        $dormitories = $dormitoriesQuery->when($this->genderScope(), fn($q, $g) => $q->where('gender', $g))
            ->orderBy('name')
            ->get();

        $kelasQuery = MadrasahKelas::where('is_active', true);
        if ($config->target_type === 'kelas' && !empty($config->target_filters)) {
            $kelasQuery->whereIn('id', collect($config->target_filters)->flatten()->toArray());
        }
        $kelasList = $kelasQuery->orderBy('name')->get();

        return view('livewire.keuangan.billing-configuration-print-setup', [
            'config' => $config,
            'dormitories' => $dormitories,
            'kelasList' => $kelasList,
            'preview' => $this->previewData,
        ])->layout('layouts.app');
    }
}
