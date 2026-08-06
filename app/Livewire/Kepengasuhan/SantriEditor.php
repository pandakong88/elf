<?php

namespace App\Livewire\Kepengasuhan;

use App\Livewire\Concerns\SendsToast;
use App\Modules\Core\Models\Person;
use App\Modules\Kepengasuhan\Models\SantriProfile;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class SantriEditor extends Component
{
    use SendsToast;

    public string $personId;
    public ?Person $person = null;

    // Seksi 1: Data Pribadi
    public string $formName       = '';
    public string $formPhone      = '';
    public string $formBirthPlace = '';
    public string $formBirthDate  = '';
    public string $formAddress    = '';
    public string $formNotes      = '';

    // Seksi 2: Data Pendidikan Formal
    public string $formSchoolName   = '';
    public string $formSchoolYear   = '';
    public string $formBloodType    = '';
    public string $formMedicalHistory = '';
    public string $formAllergies    = '';

    // Seksi 3: Data Ayah
    public string $formFatherName       = '';
    public string $formFatherPhone      = '';
    public string $formFatherOccupation = '';
    public string $formFatherAddress    = '';

    // Seksi 4: Data Ibu
    public string $formMotherName       = '';
    public string $formMotherPhone      = '';
    public string $formMotherOccupation = '';
    public string $formMotherAddress    = '';

    // Seksi 5: Data Wali (Non-Orang Tua)
    public string $formGuardianName         = '';
    public string $formGuardianPhone        = '';
    public string $formGuardianRelationship = '';

    // History
    public $activityLogs;

    protected function rules(): array
    {
        return [
            'formName'       => 'required|string|max:255',
            'formPhone'      => 'nullable|string|max:20',
            'formBirthPlace' => 'nullable|string|max:100',
            'formBirthDate'  => 'nullable|date',
            'formAddress'    => 'nullable|string|max:500',
            'formNotes'      => 'nullable|string|max:1000',

            'formSchoolName'     => 'nullable|string|max:255',
            'formSchoolYear'     => 'nullable|string|max:50',
            'formBloodType'      => 'nullable|in:A,B,AB,O,A+,A-,B+,B-,AB+,AB-,O+,O-',
            'formMedicalHistory' => 'nullable|string|max:500',
            'formAllergies'      => 'nullable|string|max:500',

            'formFatherName'       => 'nullable|string|max:255',
            'formFatherPhone'      => 'nullable|string|max:20',
            'formFatherOccupation' => 'nullable|string|max:100',
            'formFatherAddress'    => 'nullable|string|max:500',

            'formMotherName'       => 'nullable|string|max:255',
            'formMotherPhone'      => 'nullable|string|max:20',
            'formMotherOccupation' => 'nullable|string|max:100',
            'formMotherAddress'    => 'nullable|string|max:500',

            'formGuardianName'         => 'nullable|string|max:255',
            'formGuardianPhone'        => 'nullable|string|max:20',
            'formGuardianRelationship' => 'nullable|string|max:50',
        ];
    }

    public function mount(string $personId): void
    {
        abort_if(!auth()->user()->can('update-person'), 403, 'Anda tidak memiliki izin untuk mengedit biodata santri.');

        $this->personId = $personId;
        $this->person   = Person::with('santriProfile')->findOrFail($personId);

        $this->loadForm();
        $this->loadActivityLogs();
    }

    protected function loadForm(): void
    {
        $p    = $this->person;
        $prof = $p->santriProfile;

        $this->formName       = $p->name;
        $this->formPhone      = $p->phone ?? '';
        $this->formBirthPlace = $p->birth_place ?? '';
        $this->formBirthDate  = $p->birth_date?->format('Y-m-d') ?? '';
        $this->formAddress    = $p->address ?? '';
        $this->formNotes      = $p->notes ?? '';

        $this->formSchoolName     = $prof?->school_name ?? '';
        $this->formSchoolYear     = $prof?->school_year ?? '';
        $this->formBloodType      = $prof?->blood_type ?? '';
        $this->formMedicalHistory = $prof?->medical_history ?? '';
        $this->formAllergies      = $prof?->allergies ?? '';

        $this->formFatherName       = $prof?->father_name ?? '';
        $this->formFatherPhone      = $prof?->father_phone ?? '';
        $this->formFatherOccupation = $prof?->father_occupation ?? '';
        $this->formFatherAddress    = $prof?->getAdditional('father_address', '') ?? '';

        $this->formMotherName       = $prof?->mother_name ?? '';
        $this->formMotherPhone      = $prof?->mother_phone ?? '';
        $this->formMotherOccupation = $prof?->getAdditional('mother_job', '') ?? '';
        $this->formMotherAddress    = $prof?->getAdditional('mother_address', '') ?? '';

        $this->formGuardianName         = $prof?->getAdditional('guardian_name', '') ?? '';
        $this->formGuardianPhone        = $prof?->getAdditional('guardian_phone', '') ?? '';
        $this->formGuardianRelationship = $prof?->getAdditional('guardian_relationship', '') ?? '';
    }

    protected function loadActivityLogs(): void
    {
        $this->activityLogs = \Spatie\Activitylog\Models\Activity::forSubject($this->person)
            ->with('causer')
            ->latest()
            ->limit(20)
            ->get();
    }

    public function save(): void
    {
        $this->validate();

        try {
            DB::transaction(function () {
                $oldData = [
                    'name'        => $this->person->name,
                    'phone'       => $this->person->phone,
                    'birth_place' => $this->person->birth_place,
                    'birth_date'  => $this->person->birth_date?->format('Y-m-d'),
                    'address'     => $this->person->address,
                ];

                // Update Person
                $this->person->update([
                    'name'        => $this->formName,
                    'phone'       => $this->formPhone ?: null,
                    'birth_place' => $this->formBirthPlace ?: null,
                    'birth_date'  => $this->formBirthDate ?: null,
                    'address'     => $this->formAddress ?: null,
                    'notes'       => $this->formNotes ?: null,
                ]);

                // Update SantriProfile
                $profile = $this->person->santriProfile;
                if ($profile) {
                    $additionalInfo = $profile->additional_info ?? [];
                    $additionalInfo['father_address']        = $this->formFatherAddress;
                    $additionalInfo['mother_job']            = $this->formMotherOccupation;
                    $additionalInfo['mother_address']        = $this->formMotherAddress;
                    $additionalInfo['guardian_name']         = $this->formGuardianName ?: null;
                    $additionalInfo['guardian_phone']        = $this->formGuardianPhone ?: null;
                    $additionalInfo['guardian_relationship'] = $this->formGuardianRelationship ?: null;

                    $profile->update([
                        'school_name'     => $this->formSchoolName ?: null,
                        'school_year'     => $this->formSchoolYear ?: null,
                        'blood_type'      => $this->formBloodType ?: null,
                        'medical_history' => $this->formMedicalHistory ?: null,
                        'allergies'       => $this->formAllergies ?: null,
                        'father_name'     => $this->formFatherName ?: null,
                        'father_phone'    => $this->formFatherPhone ?: null,
                        'father_occupation' => $this->formFatherOccupation ?: null,
                        'mother_name'     => $this->formMotherName ?: null,
                        'mother_phone'    => $this->formMotherPhone ?: null,
                        'additional_info' => $additionalInfo,
                    ]);
                }

                // Activity Log
                $newData = [
                    'name'        => $this->formName,
                    'phone'       => $this->formPhone,
                    'birth_place' => $this->formBirthPlace,
                    'birth_date'  => $this->formBirthDate,
                    'address'     => $this->formAddress,
                ];

                $changes = array_filter(
                    array_map(fn($k) => $oldData[$k] !== ($newData[$k] ?: null) ? $k : null, array_keys($oldData)),
                    fn($v) => $v !== null
                );

                $description = count($changes) > 0
                    ? 'Biodata diperbarui: ' . implode(', ', $changes)
                    : 'Data profil santri diperbarui';

                activity()
                    ->performedOn($this->person)
                    ->causedBy(auth()->user())
                    ->withProperties(['old' => $oldData, 'new' => $newData])
                    ->log($description);
            });

            // Reload
            $this->person->refresh();
            $this->person->load('santriProfile');
            $this->loadActivityLogs();

            $this->toastSuccess('Biodata santri berhasil disimpan.');
        } catch (\Exception $e) {
            $this->toastError('Gagal menyimpan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.kepengasuhan.santri-editor')
            ->layout('layouts.app', ['title' => 'Edit Biodata — ' . $this->person->name]);
    }
}
