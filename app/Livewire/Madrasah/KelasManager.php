<?php

namespace App\Livewire\Madrasah;

use Livewire\Component;
use Livewire\WithPagination;
use App\Modules\Madrasah\Models\MadrasahKelas;
use App\Modules\Madrasah\Models\MadrasahEnrollment;
use App\Modules\Core\Models\Person;
use Illuminate\Support\Str;

class KelasManager extends Component
{
    use WithPagination;

    // Tabs
    public string $activeTab = 'list';

    // Form: Buat/Edit Kelas
    public ?string $editingKelasId = null;
    public string $formName        = '';
    public string $formJenjang     = 'ula';
    public string $formAcademicYear = '';
    public ?string $formWaliKelasId = null;
    public bool $formIsActive      = true;

    // Tab: Assign Santri
    public ?string $selectedKelasId = null;
    public string $searchSantri     = '';
    public string $enrollAcademicYear = '';

    // Flash
    public ?string $successMessage = null;
    public ?string $errorMessage   = null;

    protected $queryString = [
        'activeTab' => ['except' => 'list'],
    ];

    public function mount(): void
    {
        $currentYear = (int) now()->format('Y');
        $this->formAcademicYear  = $currentYear . '/' . ($currentYear + 1);
        $this->enrollAcademicYear = $currentYear . '/' . ($currentYear + 1);
    }

    // ─── CRUD Kelas ────────────────────────────────────────────────

    public function saveKelas(): void
    {
        $this->validate([
            'formName'         => 'required|string|max:100',
            'formJenjang'      => 'required|in:ula,wustho,ulya',
            'formAcademicYear' => 'required|string|max:20',
        ]);

        $data = [
            'name'          => $this->formName,
            'jenjang'       => $this->formJenjang,
            'academic_year' => $this->formAcademicYear,
            'wali_kelas_id' => $this->formWaliKelasId ?: null,
            'is_active'     => $this->formIsActive,
            'created_by'    => auth()->id(),
        ];

        if ($this->editingKelasId) {
            MadrasahKelas::findOrFail($this->editingKelasId)->update($data);
            $this->successMessage = 'Kelas berhasil diperbarui.';
        } else {
            MadrasahKelas::create($data);
            $this->successMessage = 'Kelas baru berhasil dibuat.';
        }

        $this->resetForm();
        $this->activeTab = 'list';
    }

    public function editKelas(string $id): void
    {
        $kelas = MadrasahKelas::findOrFail($id);
        $this->editingKelasId  = $id;
        $this->formName        = $kelas->name;
        $this->formJenjang     = $kelas->jenjang;
        $this->formAcademicYear = $kelas->academic_year;
        $this->formWaliKelasId = $kelas->wali_kelas_id;
        $this->formIsActive    = $kelas->is_active;
        $this->activeTab       = 'form';
    }

    public function deleteKelas(string $id): void
    {
        $kelas = MadrasahKelas::findOrFail($id);
        if ($kelas->enrollments()->exists()) {
            $this->errorMessage = 'Kelas tidak bisa dihapus karena masih ada santri terdaftar.';
            return;
        }
        $kelas->delete();
        $this->successMessage = 'Kelas berhasil dihapus.';
    }

    public function resetForm(): void
    {
        $this->editingKelasId  = null;
        $this->formName        = '';
        $this->formJenjang     = 'ula';
        $this->formIsActive    = true;
    }

    // ─── Assign Santri ke Kelas ────────────────────────────────────

    public function enrollSantri(string $personId): void
    {
        if (!$this->selectedKelasId) return;

        try {
            MadrasahEnrollment::create([
                'person_id'     => $personId,
                'kelas_id'      => $this->selectedKelasId,
                'academic_year' => $this->enrollAcademicYear,
                'is_active'     => true,
                'created_by'    => auth()->id(),
            ]);
            $this->successMessage = 'Santri berhasil didaftarkan ke kelas.';
        } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
            $this->errorMessage = 'Santri sudah terdaftar di kelas dan tahun ajaran ini.';
        }

        $this->searchSantri = '';
    }

    public function unenrollSantri(string $enrollmentId): void
    {
        MadrasahEnrollment::findOrFail($enrollmentId)->delete();
        $this->successMessage = 'Santri berhasil dikeluarkan dari kelas.';
    }

    // ─── Render ────────────────────────────────────────────────────

    public function render()
    {
        // Daftar semua kelas
        $kelasList = MadrasahKelas::with('waliKelas')
            ->orderBy('jenjang')
            ->orderBy('name')
            ->paginate(15);

        // Pencarian santri untuk enrollment
        $santriSearchResults = [];
        if (strlen($this->searchSantri) >= 3 && $this->selectedKelasId) {
            // Santri yang sudah terdaftar di kelas ini (untuk di-exclude dari hasil pencarian)
            $enrolled = MadrasahEnrollment::where('kelas_id', $this->selectedKelasId)
                ->where('academic_year', $this->enrollAcademicYear)
                ->pluck('person_id');

            $santriSearchResults = Person::whereHas('activeRoles', fn($q) =>
                $q->where('role_type', 'santri')
            )
            ->whereNotIn('id', $enrolled)
            ->where('name', 'like', '%' . $this->searchSantri . '%')
            ->limit(8)
            ->get();
        }

        // Daftar santri di kelas terpilih
        $enrolledSantri = collect();
        $selectedKelas  = null;
        if ($this->selectedKelasId) {
            $selectedKelas  = MadrasahKelas::find($this->selectedKelasId);
            $enrolledSantri = MadrasahEnrollment::with('person')
                ->where('kelas_id', $this->selectedKelasId)
                ->where('academic_year', $this->enrollAcademicYear)
                ->where('is_active', true)
                ->get();
        }

        // Daftar kelas untuk dropdown assign
        $kelasOptions = MadrasahKelas::where('is_active', true)
            ->orderBy('jenjang')->orderBy('name')
            ->get();

        // Guru/wali kelas options
        $guruOptions = Person::whereHas('activeRoles', fn($q) =>
            $q->where('role_type', 'guru')
        )->orderBy('name')->get();

        return view('livewire.madrasah.kelas-manager', [
            'kelasList'          => $kelasList,
            'santriSearchResults' => $santriSearchResults,
            'enrolledSantri'     => $enrolledSantri,
            'selectedKelas'      => $selectedKelas,
            'kelasOptions'       => $kelasOptions,
            'guruOptions'        => $guruOptions,
        ])->layout('layouts.app');
    }
}
