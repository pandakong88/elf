<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Modules\Core\Models\Person;
use App\Modules\Keuangan\Models\Bill;
use App\Modules\Kepengasuhan\Models\Dormitory;
use App\Modules\Madrasah\Models\MadrasahKelas;
use App\Modules\Madrasah\Models\MadrasahEnrollment;
use Illuminate\View\View;

use App\Modules\Keuangan\Models\BillingConfiguration;
use App\Traits\HasGenderScope;

class KeuanganPrintController extends Controller
{
    use HasGenderScope;

    public function checklistKomplek(Request $request): View
    {
        $dormitoryId = $request->query('dormitory_id');
        if (is_array($dormitoryId)) {
            $dormitoryId = head($dormitoryId);
        }
        $dormitoryId = trim($dormitoryId);

        $billType    = $request->query('bill_type', 'syahriah_pondok');
        $month       = (int) $request->query('month', now()->month);
        $year        = (int) $request->query('year', now()->year);

        $dormitory = Dormitory::findOrFail($dormitoryId);

        $query = Person::whereHas('activeRoles', fn($q) =>
            $q->where('role_type', 'santri')->where('enrollment_status', 'aktif')
        )
        ->whereIn('id', function($q) use ($dormitoryId) {
            $q->select('person_id')
              ->from('room_assignments')
              ->join('rooms', 'rooms.id', '=', 'room_assignments.room_id')
              ->where('rooms.dormitory_id', $dormitoryId)
              ->where('room_assignments.is_active', true)
              ->where('room_assignments.valid_from', '<=', now())
              ->where(function ($sub) {
                  $sub->whereNull('room_assignments.valid_until')
                      ->orWhere('room_assignments.valid_until', '>=', now());
              });
        })
        ->when($this->genderScope(), fn($q, $g) => $q->where('gender', $g))
        ->orderBy('name');

        $santriList = $query->get();

        // Get relevant 4 months columns
        $months = [];
        for ($i = 3; $i >= 0; $i--) {
            $date = now()->setDate($year, $month, 1)->subMonths($i);
            $key  = $date->format('n') . '-' . $date->format('Y');
            $months[$key] = $date->locale('id')->translatedFormat('M Y');
        }

        $gridData = $santriList->map(function ($santri) use ($months, $billType, $month, $year) {
            $bills = [];
            foreach ($months as $periodKey => $periodLabel) {
                [$m, $y] = explode('-', $periodKey);
                $bill = Bill::where('person_id', $santri->id)
                    ->where('bill_type', $billType)
                    ->where('period_month', (int)$m)
                    ->where('period_year', (int)$y)
                    ->first();
                $bills[$periodKey] = $bill;
            }

            // Calculate oldest display month boundary
            $firstMonthKey = array_key_first($months);
            [$firstM, $firstY] = explode('-', $firstMonthKey);

            // Query accumulated unpaid bills older than the oldest displayed month
            $tunggakanLama = Bill::where('person_id', $santri->id)
                ->where('bill_type', $billType)
                ->whereIn('status', ['unpaid', 'partial'])
                ->where(function($q) use ($firstM, $firstY) {
                    $q->where('period_year', '<', (int)$firstY)
                      ->orWhere(function($sub) use ($firstM, $firstY) {
                          $sub->where('period_year', (int)$firstY)
                              ->where('period_month', '<', (int)$firstM);
                      });
                })
                ->get();

            $tunggakanLamaSum = $tunggakanLama->sum(fn($b) => $b->amount - $b->amount_paid);

            // Query prepaid until label (furthest future paid month)
            $furthestPaidBill = Bill::where('person_id', $santri->id)
                ->where('bill_type', $billType)
                ->where('status', 'paid')
                ->where(function($q) use ($month, $year) {
                    $q->where('period_year', '>', (int)$year)
                      ->orWhere(function($sub) use ($month, $year) {
                          $sub->where('period_year', (int)$year)
                              ->where('period_month', '>', (int)$month);
                      });
                })
                ->orderBy('period_year', 'desc')
                ->orderBy('period_month', 'desc')
                ->first();

            $lunasDiMukaLabel = null;
            if ($furthestPaidBill) {
                $lunasDiMukaLabel = \Carbon\Carbon::create($furthestPaidBill->period_year, $furthestPaidBill->period_month, 1)
                    ->locale('id')
                    ->translatedFormat('M Y');
            }

            return [
                'person' => $santri,
                'bills' => $bills,
                'tunggakanLamaSum' => $tunggakanLamaSum,
                'lunasDiMukaLabel' => $lunasDiMukaLabel,
            ];
        });

        return view('print.checklist-komplek', compact('dormitory', 'billType', 'months', 'gridData', 'month', 'year'));
    }

    public function checklistKelas(Request $request): View
    {
        $kelasIdStr = $request->query('kelas_ids') ?: $request->query('kelas_id');
        $kelasIds = [];
        if ($kelasIdStr) {
            if (is_array($kelasIdStr)) {
                $kelasIds = collect($kelasIdStr)->flatten()->toArray();
            } else {
                $kelasIds = explode(',', $kelasIdStr);
            }
        }
        $kelasIds = array_filter(array_map('trim', $kelasIds));

        $billType = $request->query('bill_type', 'syahriah_madrasah');
        $semester = (int) $request->query('semester', now()->month <= 6 ? 1 : 2);
        $year     = (int) $request->query('year', now()->year);
        $paperSize = $request->query('paper_size', 'a4');
        $pageBreakRoom = (bool) $request->query('page_break_room', false);

        $kelasList = MadrasahKelas::whereIn('id', $kelasIds)->get();
        $dormitories = collect();

        $config = BillingConfiguration::where('type', $billType)->first();
        if (!$config) {
            $config = new BillingConfiguration([
                'type' => $billType,
                'label' => ucwords(str_replace('_', ' ', $billType)),
                'amount' => 0,
                'interval' => 'semester',
                'target_type' => 'kelas',
            ]);
        }

        $santriList = Person::select(
            'persons.*',
            'madrasah_kelas.name as kelas_name',
            'madrasah_kelas.academic_year as kelas_academic_year',
            'madrasah_kelas.jenjang as kelas_jenjang',
            'rooms.name as room_name',
            'dormitories.name as dormitory_name'
        )
            ->join('madrasah_enrollments', 'madrasah_enrollments.person_id', '=', 'persons.id')
            ->join('madrasah_kelas', 'madrasah_kelas.id', '=', 'madrasah_enrollments.kelas_id')
            ->leftJoin('room_assignments', function($join) {
                $join->on('room_assignments.person_id', '=', 'persons.id')
                     ->where('room_assignments.is_active', '=', true);
            })
            ->leftJoin('rooms', 'rooms.id', '=', 'room_assignments.room_id')
            ->leftJoin('dormitories', 'dormitories.id', '=', 'rooms.dormitory_id')
            ->whereIn('madrasah_enrollments.kelas_id', $kelasIds)
            ->where('madrasah_enrollments.is_active', true)
            ->when($this->genderScope(), fn($q, $g) => $q->where('persons.gender', $g))
            ->orderBy('madrasah_kelas.name')
            ->orderBy('persons.name')
            ->get();

        $exceptions = $config->id 
            ? \App\Modules\Keuangan\Models\BillingException::where('billing_config_id', $config->id)->get()->keyBy('person_id')
            : collect();

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

        if ($config->can_be_installment) {
            $maxTerms = Bill::where('billing_config_id', $config->id)
                ->whereNotNull('parent_bill_id')
                ->whereIn('person_id', $santriList->pluck('id'))
                ->get()
                ->groupBy('parent_bill_id')
                ->map(fn($group) => $group->count())
                ->max() ?: 3;

            $terms = range(1, $maxTerms);

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
                    $bills[$t] = $childBills->get($t - 1);
                }

                $jenjangLabel = match ($santri->kelas_jenjang) {
                    'ula'    => 'Ula (Ibtidaiyah)',
                    'wustho' => 'Wustho (Tsanawiyah)',
                    'ulya'   => 'Ulya (Aliyah)',
                    default  => $santri->kelas_jenjang,
                };

                $tariffInfo = $getExpectedTariff($santri->id);

                return [
                    'person' => $santri,
                    'kelas_name' => $santri->kelas_name,
                    'kelas_academic_year' => $santri->kelas_academic_year,
                    'kelas_jenjang' => $jenjangLabel,
                    'room_name' => $santri->room_name,
                    'dormitory_name' => $santri->dormitory_name,
                    'parentBill' => $parentBill,
                    'bills' => $bills,
                    'expectedAmount' => $tariffInfo['amount'],
                    'exceptionNote' => $tariffInfo['note'],
                    'tunggakanLamaSum' => 0.00,
                ];
            });

            return view('print.checklist-config-installment', compact('config', 'dormitories', 'kelasList', 'terms', 'gridData', 'paperSize', 'pageBreakRoom'));
        } else {
            $layoutType = $config->interval;

            if (in_array($layoutType, ['semester', '2x_yearly', 'caturwulan', '3x_yearly', 'triwulan', '4x_yearly'])) {
                if (in_array($layoutType, ['caturwulan', '3x_yearly'])) {
                    $periods = [
                        "1-{$year}" => "Caturwulan 1",
                        "2-{$year}" => "Caturwulan 2",
                        "3-{$year}" => "Caturwulan 3",
                    ];
                } elseif (in_array($layoutType, ['triwulan', '4x_yearly'])) {
                    $periods = [
                        "1-{$year}" => "Triwulan 1",
                        "2-{$year}" => "Triwulan 2",
                        "3-{$year}" => "Triwulan 3",
                        "4-{$year}" => "Triwulan 4",
                    ];
                } else {
                    $periods = [];
                    $prevSem  = $semester === 1 ? 2 : 1;
                    $prevYear = $semester === 1 ? $year - 1 : $year;
                    $periods["{$prevSem}-{$prevYear}"] = "Sem {$prevSem} / {$prevYear}";
                    $periods["{$semester}-{$year}"]    = "Sem {$semester} / {$year}";
                }

                $gridData = $santriList->map(function ($santri) use ($periods, $config, $year, $semester, $getExpectedTariff) {
                    $bills = [];
                    foreach ($periods as $periodKey => $periodLabel) {
                        [$sem, $yr] = explode('-', $periodKey);
                        $bill = Bill::where('person_id', $santri->id)
                            ->where('billing_config_id', $config->id)
                            ->where('period_month', (int)$sem)
                            ->where('period_year', (int)$yr)
                            ->first();
                        $bills[$periodKey] = $bill;
                    }

                    $firstPeriodKey = array_key_first($periods);
                    [$firstSem, $firstYr] = explode('-', $firstPeriodKey);
                    $tunggakanLama = Bill::where('person_id', $santri->id)
                        ->where('billing_config_id', $config->id)
                        ->whereIn('status', ['unpaid', 'partial'])
                        ->where(function($q) use ($firstSem, $firstYr) {
                            $q->where('period_year', '<', (int)$firstYr)
                              ->orWhere(function($sub) use ($firstSem, $firstYr) {
                                  $sub->where('period_year', (int)$firstYr)
                                      ->where('period_month', '<', (int)$firstSem);
                              });
                        })
                        ->get();
                    $tunggakanLamaSum = $tunggakanLama->sum(fn($b) => $b->amount - $b->amount_paid);

                    $furthestPaidBill = Bill::where('person_id', $santri->id)
                        ->where('billing_config_id', $config->id)
                        ->where('status', 'paid')
                        ->where(function($q) use ($semester, $year) {
                            $q->where('period_year', '>', (int)$year)
                              ->orWhere(function($sub) use ($semester, $year) {
                                  $sub->where('period_year', (int)$year)
                                      ->where('period_month', '>', (int)$semester);
                              });
                        })
                        ->orderBy('period_year', 'desc')
                        ->orderBy('period_month', 'desc')
                        ->first();

                    $lunasDiMukaLabel = null;
                    if ($furthestPaidBill) {
                        $lunasDiMukaLabel = "Sem {$furthestPaidBill->period_month} / {$furthestPaidBill->period_year}";
                    }

                    $jenjangLabel = match ($santri->kelas_jenjang) {
                        'ula'    => 'Ula (Ibtidaiyah)',
                        'wustho' => 'Wustho (Tsanawiyah)',
                        'ulya'   => 'Ulya (Aliyah)',
                        default  => $santri->kelas_jenjang,
                    };

                    $tariffInfo = $getExpectedTariff($santri->id);

                    return [
                        'person' => $santri,
                        'kelas_name' => $santri->kelas_name,
                        'kelas_academic_year' => $santri->kelas_academic_year,
                        'kelas_jenjang' => $jenjangLabel,
                        'room_name' => $santri->room_name,
                        'dormitory_name' => $santri->dormitory_name,
                        'bills' => $bills,
                        'expectedAmount' => $tariffInfo['amount'],
                        'exceptionNote' => $tariffInfo['note'],
                        'tunggakanLamaSum' => $tunggakanLamaSum,
                        'lunasDiMukaLabel' => $lunasDiMukaLabel,
                    ];
                });

                return view('print.checklist-config-semester', compact('config', 'dormitories', 'kelasList', 'periods', 'gridData', 'year', 'paperSize', 'pageBreakRoom'));
            } elseif ($layoutType === 'monthly') {
                $months = [];
                for ($m = 1; $m <= 12; $m++) {
                    $date = now()->setDate($year, $m, 1);
                    $key  = $m . '-' . $year;
                    $months[$key] = $date->locale('id')->translatedFormat('M');
                }

                $gridData = $santriList->map(function ($santri) use ($months, $config, $year, $getExpectedTariff) {
                    $bills = [];
                    foreach ($months as $periodKey => $periodLabel) {
                        [$m, $y] = explode('-', $periodKey);
                        $bill = Bill::where('person_id', $santri->id)
                            ->where('billing_config_id', $config->id)
                            ->where('period_month', (int)$m)
                            ->where('period_year', (int)$y)
                            ->first();
                        $bills[$periodKey] = $bill;
                    }

                    $tunggakanLama = Bill::where('person_id', $santri->id)
                        ->where('billing_config_id', $config->id)
                        ->whereIn('status', ['unpaid', 'partial'])
                        ->where('period_year', '<', $year)
                        ->get();

                    $tunggakanLamaSum = $tunggakanLama->sum(fn($b) => $b->amount - $b->amount_paid);

                    $furthestPaidBill = Bill::where('person_id', $santri->id)
                        ->where('billing_config_id', $config->id)
                        ->where('status', 'paid')
                        ->where('period_year', '>', $year)
                        ->orderBy('period_year', 'desc')
                        ->orderBy('period_month', 'desc')
                        ->first();

                    $lunasDiMukaLabel = null;
                    if ($furthestPaidBill) {
                        $lunasDiMukaLabel = "Th " . $furthestPaidBill->period_year;
                    }

                    $jenjangLabel = match ($santri->kelas_jenjang) {
                        'ula'    => 'Ula (Ibtidaiyah)',
                        'wustho' => 'Wustho (Tsanawiyah)',
                        'ulya'   => 'Ulya (Aliyah)',
                        default  => $santri->kelas_jenjang,
                    };

                    $tariffInfo = $getExpectedTariff($santri->id);

                    return [
                        'person' => $santri,
                        'kelas_name' => $santri->kelas_name,
                        'kelas_academic_year' => $santri->kelas_academic_year,
                        'kelas_jenjang' => $jenjangLabel,
                        'room_name' => $santri->room_name,
                        'dormitory_name' => $santri->dormitory_name,
                        'bills' => $bills,
                        'expectedAmount' => $tariffInfo['amount'],
                        'exceptionNote' => $tariffInfo['note'],
                        'tunggakanLamaSum' => $tunggakanLamaSum,
                        'lunasDiMukaLabel' => $lunasDiMukaLabel,
                    ];
                });

                return view('print.checklist-config-monthly', compact('config', 'dormitories', 'kelasList', 'months', 'gridData', 'year', 'paperSize', 'pageBreakRoom'));
            } else {
                $gridData = $santriList->map(function ($santri) use ($config, $getExpectedTariff) {
                    $bill = Bill::where('person_id', $santri->id)
                        ->where('billing_config_id', $config->id)
                        ->first();

                    $jenjangLabel = match ($santri->kelas_jenjang) {
                        'ula'    => 'Ula (Ibtidaiyah)',
                        'wustho' => 'Wustho (Tsanawiyah)',
                        'ulya'   => 'Ulya (Aliyah)',
                        default  => $santri->kelas_jenjang,
                    };

                    $tariffInfo = $getExpectedTariff($santri->id);

                    return [
                        'person' => $santri,
                        'kelas_name' => $santri->kelas_name,
                        'kelas_academic_year' => $santri->kelas_academic_year,
                        'kelas_jenjang' => $jenjangLabel,
                        'room_name' => $santri->room_name,
                        'dormitory_name' => $santri->dormitory_name,
                        'bills' => ['single' => $bill],
                        'expectedAmount' => $tariffInfo['amount'],
                        'exceptionNote' => $tariffInfo['note'],
                        'tunggakanLamaSum' => 0.00,
                    ];
                });

                return view('print.checklist-config-single', compact('config', 'dormitories', 'kelasList', 'gridData', 'year', 'paperSize', 'pageBreakRoom'));
            }
        }
    }

    public function checklistConfig(string $id, Request $request): View
    {
        $config = BillingConfiguration::findOrFail($id);
        $dormitoryIdStr = $request->query('dormitory_ids') ?: $request->query('dormitory_id');
        $dormitoryIds = [];
        if ($dormitoryIdStr) {
            if (is_array($dormitoryIdStr)) {
                $dormitoryIds = collect($dormitoryIdStr)->flatten()->toArray();
            } else {
                $dormitoryIds = explode(',', $dormitoryIdStr);
            }
        }
        $dormitoryIds = array_filter(array_map('trim', $dormitoryIds));

        $dormitories = Dormitory::whereIn('id', $dormitoryIds)->get();
        
        $paperSize = $request->query('paper_size', 'a4');
        $pageBreakRoom = (bool) $request->query('page_break_room', false);

        // Fetch active santri with room assignments in selected dormitories, ordered by dormitory, room, and name
        $query = Person::select('persons.*', 'rooms.name as room_name', 'dormitories.name as dormitory_name', 'rooms.dormitory_id')
        ->whereHas('activeRoles', fn($q) =>
            $q->where('role_type', 'santri')->where('enrollment_status', 'aktif')
        )
        ->join('room_assignments', 'room_assignments.person_id', '=', 'persons.id')
        ->join('rooms', 'rooms.id', '=', 'room_assignments.room_id')
        ->join('dormitories', 'dormitories.id', '=', 'rooms.dormitory_id')
        ->whereIn('rooms.dormitory_id', $dormitoryIds)
        ->where('room_assignments.is_active', true)
        ->when($this->genderScope(), fn($q, $g) => $q->where('persons.gender', $g))
        ->orderBy('dormitories.name')
        ->orderBy('rooms.name')
        ->orderBy('persons.name');

        $santriList = $query->get();

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

        if ($config->can_be_installment) {
            // Installment-based layout
            // Determine maximum terms count for this config in database
            $maxTerms = Bill::where('billing_config_id', $config->id)
                ->whereNotNull('parent_bill_id')
                ->whereIn('person_id', $santriList->pluck('id'))
                ->get()
                ->groupBy('parent_bill_id')
                ->map(fn($group) => $group->count())
                ->max() ?: 3;

            $terms = range(1, $maxTerms);

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
                    $bills[$t] = $childBills->get($t - 1);
                }

                $tariffInfo = $getExpectedTariff($santri->id);

                return [
                    'person' => $santri,
                    'room_name' => $santri->room_name,
                    'dormitory_name' => $santri->dormitory_name,
                    'dormitory_id' => $santri->dormitory_id,
                    'parentBill' => $parentBill,
                    'bills' => $bills,
                    'expectedAmount' => $tariffInfo['amount'],
                    'exceptionNote' => $tariffInfo['note'],
                    'tunggakanLamaSum' => 0.00,
                ];
            });

            return view('print.checklist-config-installment', compact('config', 'dormitories', 'terms', 'gridData', 'paperSize', 'pageBreakRoom'));
        } else {
            $layoutType = $config->interval;

            if (in_array($layoutType, ['semester', '2x_yearly', 'caturwulan', '3x_yearly', 'triwulan', '4x_yearly'])) {
                $semester = (int) $request->query('semester', now()->month <= 6 ? 1 : 2);
                $year     = (int) $request->query('year', now()->year);

                if (in_array($layoutType, ['caturwulan', '3x_yearly'])) {
                    $periods = [
                        "1-{$year}" => "Caturwulan 1",
                        "2-{$year}" => "Caturwulan 2",
                        "3-{$year}" => "Caturwulan 3",
                    ];
                } elseif (in_array($layoutType, ['triwulan', '4x_yearly'])) {
                    $periods = [
                        "1-{$year}" => "Triwulan 1",
                        "2-{$year}" => "Triwulan 2",
                        "3-{$year}" => "Triwulan 3",
                        "4-{$year}" => "Triwulan 4",
                    ];
                } else {
                    $periods = [];
                    $prevSem  = $semester === 1 ? 2 : 1;
                    $prevYear = $semester === 1 ? $year - 1 : $year;
                    $periods["{$prevSem}-{$prevYear}"] = "Sem {$prevSem} / {$prevYear}";
                    $periods["{$semester}-{$year}"]    = "Sem {$semester} / {$year}";
                }

                $gridData = $santriList->map(function ($santri) use ($periods, $config, $year, $semester, $getExpectedTariff) {
                    $bills = [];
                    foreach ($periods as $periodKey => $periodLabel) {
                        [$sem, $yr] = explode('-', $periodKey);
                        $bill = Bill::where('person_id', $santri->id)
                            ->where('billing_config_id', $config->id)
                            ->where('period_month', (int)$sem)
                            ->where('period_year', (int)$yr)
                            ->first();
                        $bills[$periodKey] = $bill;
                    }

                    // Accumulated unpaid bills of this config type before the first period
                    $firstPeriodKey = array_key_first($periods);
                    [$firstSem, $firstYr] = explode('-', $firstPeriodKey);
                    $tunggakanLama = Bill::where('person_id', $santri->id)
                        ->where('billing_config_id', $config->id)
                        ->whereIn('status', ['unpaid', 'partial'])
                        ->where(function($q) use ($firstSem, $firstYr) {
                            $q->where('period_year', '<', (int)$firstYr)
                              ->orWhere(function($sub) use ($firstSem, $firstYr) {
                                  $sub->where('period_year', (int)$firstYr)
                                      ->where('period_month', '<', (int)$firstSem);
                              });
                        })
                        ->get();
                    $tunggakanLamaSum = $tunggakanLama->sum(fn($b) => $b->amount - $b->amount_paid);

                    // Prepaid indicator (paid bills in future periods)
                    $furthestPaidBill = Bill::where('person_id', $santri->id)
                        ->where('billing_config_id', $config->id)
                        ->where('status', 'paid')
                        ->where(function($q) use ($semester, $year) {
                            $q->where('period_year', '>', (int)$year)
                              ->orWhere(function($sub) use ($semester, $year) {
                                  $sub->where('period_year', (int)$year)
                                      ->where('period_month', '>', (int)$semester);
                              });
                        })
                        ->orderBy('period_year', 'desc')
                        ->orderBy('period_month', 'desc')
                        ->first();

                    $lunasDiMukaLabel = null;
                    if ($furthestPaidBill) {
                        $lunasDiMukaLabel = "Sem {$furthestPaidBill->period_month} / {$furthestPaidBill->period_year}";
                    }

                    $tariffInfo = $getExpectedTariff($santri->id);

                    return [
                        'person' => $santri,
                        'room_name' => $santri->room_name,
                        'dormitory_name' => $santri->dormitory_name,
                        'dormitory_id' => $santri->dormitory_id,
                        'bills' => $bills,
                        'expectedAmount' => $tariffInfo['amount'],
                        'exceptionNote' => $tariffInfo['note'],
                        'tunggakanLamaSum' => $tunggakanLamaSum,
                        'lunasDiMukaLabel' => $lunasDiMukaLabel,
                    ];
                });

                return view('print.checklist-config-semester', compact('config', 'dormitories', 'periods', 'gridData', 'year', 'paperSize', 'pageBreakRoom'));
            } elseif ($layoutType === 'monthly') {
                // Monthly/regular grid layout: 12 months for the selected calendar year
                $year = (int) $request->query('year', now()->year);

                $months = [];
                for ($m = 1; $m <= 12; $m++) {
                    $date = now()->setDate($year, $m, 1);
                    $key  = $m . '-' . $year;
                    $months[$key] = $date->locale('id')->translatedFormat('M');
                }

                $gridData = $santriList->map(function ($santri) use ($months, $config, $year, $getExpectedTariff) {
                    $bills = [];
                    foreach ($months as $periodKey => $periodLabel) {
                        [$m, $y] = explode('-', $periodKey);
                        $bill = Bill::where('person_id', $santri->id)
                            ->where('billing_config_id', $config->id)
                            ->where('period_month', (int)$m)
                            ->where('period_year', (int)$y)
                            ->first();
                        $bills[$periodKey] = $bill;
                    }

                    // Accumulated unpaid bills of this config type before January of the selected year
                    $tunggakanLama = Bill::where('person_id', $santri->id)
                        ->where('billing_config_id', $config->id)
                        ->whereIn('status', ['unpaid', 'partial'])
                        ->where('period_year', '<', $year)
                        ->get();

                    $tunggakanLamaSum = $tunggakanLama->sum(fn($b) => $b->amount - $b->amount_paid);

                    // Prepaid indicator (paid bills in future years)
                    $furthestPaidBill = Bill::where('person_id', $santri->id)
                        ->where('billing_config_id', $config->id)
                        ->where('status', 'paid')
                        ->where('period_year', '>', $year)
                        ->orderBy('period_year', 'desc')
                        ->orderBy('period_month', 'desc')
                        ->first();

                    $lunasDiMukaLabel = null;
                    if ($furthestPaidBill) {
                        $lunasDiMukaLabel = "Th " . $furthestPaidBill->period_year;
                    }

                    $tariffInfo = $getExpectedTariff($santri->id);

                    return [
                        'person' => $santri,
                        'room_name' => $santri->room_name,
                        'dormitory_name' => $santri->dormitory_name,
                        'dormitory_id' => $santri->dormitory_id,
                        'bills' => $bills,
                        'expectedAmount' => $tariffInfo['amount'],
                        'exceptionNote' => $tariffInfo['note'],
                        'tunggakanLamaSum' => $tunggakanLamaSum,
                        'lunasDiMukaLabel' => $lunasDiMukaLabel,
                    ];
                });

                return view('print.checklist-config-monthly', compact('config', 'dormitories', 'months', 'gridData', 'year', 'paperSize', 'pageBreakRoom'));
            } else {
                // Yearly / once / insidental (single column checklist)
                $year = (int) $request->query('year', now()->year);
                $gridData = $santriList->map(function ($santri) use ($config, $getExpectedTariff) {
                    $bill = Bill::where('person_id', $santri->id)
                        ->where('billing_config_id', $config->id)
                        ->first();

                    $tariffInfo = $getExpectedTariff($santri->id);

                    return [
                        'person' => $santri,
                        'room_name' => $santri->room_name,
                        'dormitory_name' => $santri->dormitory_name,
                        'dormitory_id' => $santri->dormitory_id,
                        'bills' => ['single' => $bill],
                        'expectedAmount' => $tariffInfo['amount'],
                        'exceptionNote' => $tariffInfo['note'],
                        'tunggakanLamaSum' => 0.00,
                    ];
                });

                return view('print.checklist-config-single', compact('config', 'dormitories', 'gridData', 'year', 'paperSize', 'pageBreakRoom'));
            }
        }
    }
}
