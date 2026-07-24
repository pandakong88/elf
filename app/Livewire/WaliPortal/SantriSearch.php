<?php

namespace App\Livewire\WaliPortal;

use Livewire\Component;
use Livewire\WithPagination;
use App\Modules\Core\Models\Person;
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
        $dormitories = Dormitory::orderBy('name')->get();
        $rooms = Room::when($this->filterKomplek, fn($q) => $q->where('dormitory_id', $this->filterKomplek))
            ->orderBy('name')
            ->get();
        $kelases = MadrasahKelas::orderBy('name')->get();

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

        return view('livewire.wali-portal.santri-search', [
            'dormitories' => $dormitories,
            'rooms'       => $rooms,
            'kelases'     => $kelases,
            'santris'     => $santris,
        ])->layout('layouts.wali-portal', ['title' => 'Cari Santri — Portal Wali']);
    }
}
