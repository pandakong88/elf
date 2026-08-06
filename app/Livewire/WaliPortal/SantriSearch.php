<?php

namespace App\Livewire\WaliPortal;

use Livewire\Component;
use Livewire\WithPagination;
use App\Modules\Core\Models\Person;
use App\Modules\Core\Models\LandingPageContent;
use App\Modules\Kepengasuhan\Models\Dormitory;
use App\Modules\Kepengasuhan\Models\Room;
use App\Modules\Madrasah\Models\MadrasahKelas;

class SantriSearch extends Component
{
    use WithPagination;

    public string $searchName = '';
    public string $filterKomplek = '';
    public string $filterKamar = '';
    public string $filterKelas = '';

    public function updatedFilterKomplek()
    {
        $this->filterKamar = '';
        $this->resetPage();
    }

    public function updatedSearchName()
    {
        $this->resetPage();
    }

    public function updatedFilterKamar()
    {
        $this->resetPage();
    }

    public function updatedFilterKelas()
    {
        $this->resetPage();
    }

    public function render()
    {
        // Natural Sorting for all dropdown options
        $dormitories = Dormitory::all()->sort(fn($a, $b) => strnatcasecmp($a->name, $b->name));

        $rooms = Room::when($this->filterKomplek, fn($q) => $q->where('dormitory_id', $this->filterKomplek))
            ->get()
            ->sort(fn($a, $b) => strnatcasecmp($a->name, $b->name));

        $kelases = MadrasahKelas::where('is_active', true)
            ->get()
            ->sort(fn($a, $b) => strnatcasecmp($a->name, $b->name));

        $hasQuery = trim($this->searchName) !== '' || $this->filterKomplek !== '' || $this->filterKamar !== '' || $this->filterKelas !== '';

        $totalSantriCount = Person::whereHas('roles', function ($q) {
            $q->where('role_type', 'santri')->where('is_active', true);
        })->count();

        $santris = null;
        if ($hasQuery) {
            $query = Person::query()
                ->whereHas('roles', function ($q) {
                    $q->where('role_type', 'santri')->where('is_active', true);
                })
                ->with([
                    'roomAssignments' => function ($q) {
                        $q->where('is_active', true)->with('room.dormitory');
                    },
                    'madrasahEnrollments' => function ($q) {
                        $q->where('is_active', true)->with('kelas');
                    },
                    'santriProfile'
                ]);

            if (trim($this->searchName) !== '') {
                $query->where('name', 'like', '%' . trim($this->searchName) . '%');
            }

            if ($this->filterKomplek) {
                $query->whereHas('roomAssignments', function ($q) {
                    $q->where('is_active', true)
                      ->whereHas('room', function ($rq) {
                          $rq->where('dormitory_id', $this->filterKomplek);
                      });
                });
            }

            if ($this->filterKamar) {
                $query->whereHas('roomAssignments', function ($q) {
                    $q->where('is_active', true)
                      ->where('room_id', $this->filterKamar);
                });
            }

            if ($this->filterKelas) {
                $query->whereHas('madrasahEnrollments', function ($q) {
                    $q->where('is_active', true)
                      ->where('kelas_id', $this->filterKelas);
                });
            }

            $santris = $query->orderBy('name')->paginate(12);
        }

        $contents = LandingPageContent::all()->pluck('value', 'key')->toArray();

        $putraData = [
            'bank1_name' => $contents['wali_bank1_name_putra'] ?? 'Bank Syariah Indonesia (BSI)',
            'bsi'        => $contents['wali_bsi_putra'] ?? '7123456789',
            'bsi_an'     => $contents['wali_bsi_putra_an'] ?? 'Pesantren Al-Fithroh Putra',
            'bank2_name' => $contents['wali_bank2_name_putra'] ?? 'Bank BRI',
            'bri'        => $contents['wali_bri_putra'] ?? '',
            'bri_an'     => $contents['wali_bri_putra_an'] ?? '',
            'wa'         => $contents['wali_wa_putra'] ?? '6281234567890',
            'wa_name'    => $contents['wali_wa_putra_name'] ?? 'Bendahara Putra Al-Fithroh',
            'wa_url'     => 'https://wa.me/' . preg_replace('/[^0-9]/', '', $contents['wali_wa_putra'] ?? '6281234567890') . '?text=' . urlencode("Assalamu'alaikum Bendahara Putra Al-Fithroh, saya ingin bertanya info seputar tagihan santri."),
        ];

        $putriData = [
            'bank1_name' => $contents['wali_bank1_name_putri'] ?? 'Bank Syariah Indonesia (BSI)',
            'bsi'        => $contents['wali_bsi_putri'] ?? '',
            'bsi_an'     => $contents['wali_bsi_putri_an'] ?? 'Pesantren Al-Fithroh Putri',
            'bank2_name' => $contents['wali_bank2_name_putri'] ?? 'Bank BRI',
            'bri'        => $contents['wali_bri_putri'] ?? '',
            'bri_an'     => $contents['wali_bri_putri_an'] ?? '',
            'wa'         => $contents['wali_wa_putri'] ?? '6285713285438',
            'wa_name'    => $contents['wali_wa_putri_name'] ?? 'Bendahara Putri Al-Fithroh',
            'wa_url'     => 'https://wa.me/' . preg_replace('/[^0-9]/', '', $contents['wali_wa_putri'] ?? '6285713285438') . '?text=' . urlencode("Assalamu'alaikum Bendahara Putri Al-Fithroh, saya ingin bertanya info seputar tagihan santri."),
        ];

        $waliAnnouncement = $contents['wali_announcement'] ?? '';

        return view('livewire.wali-portal.santri-search', [
            'dormitories'      => $dormitories,
            'rooms'            => $rooms,
            'kelases'          => $kelases,
            'santris'          => $santris,
            'hasQuery'         => $hasQuery,
            'totalSantriCount' => $totalSantriCount,
            'putraData'        => $putraData,
            'putriData'        => $putriData,
            'waliAnnouncement' => $waliAnnouncement,
        ])->layout('layouts.wali-portal', ['title' => 'Cari Santri — Portal Wali']);
    }
}
