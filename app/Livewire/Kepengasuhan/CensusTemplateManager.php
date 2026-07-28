<?php

namespace App\Livewire\Kepengasuhan;

use Livewire\Component;
use App\Livewire\Concerns\SendsToast;
use App\Modules\Kepengasuhan\Models\CensusTemplate;
use App\Modules\Kepengasuhan\Models\CensusTemplateField;
use App\Modules\Kepengasuhan\Services\CensusV3Service;
use App\Modules\Kepengasuhan\Exports\CensusTemplateSampleExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CensusTemplateManager extends Component
{
    use SendsToast;

    // Mode: 'list', 'create', 'edit'
    public string $mode = 'list';

    // Search and filter fields for templates list
    public string $search = '';
    public string $filterType = 'all'; // 'all', 'default', 'custom'

    // Confirmation modal state
    public ?string $confirmingArchiveId = null;

    // Excel Preview state
    public ?string $previewTemplateId = null;
    public string $previewActiveTab = 'simulator'; // 'simulator', 'guide'

    // Form fields for Template
    public ?string $templateId = null;
    public string $name = '';
    public string $description = '';
    public bool $is_default = false;

    // Fields list for the builder
    public array $fields = [];

    // Temporary variables for adding fields
    public string $selectedSystemField = '';
    public array $availableSystemFields = [];

    protected CensusV3Service $censusService;

    public function boot(CensusV3Service $censusService): void
    {
        $this->censusService = $censusService;
    }

    public function mount(): void
    {
        $user = auth()->user();
        if (!$user || !($user->hasRole('super-admin') || $user->hasRole('manajemen'))) {
            abort(403, 'Akses ditolak. Halaman ini hanya untuk Super Admin dan Manajemen.');
        }

        $this->availableSystemFields = CensusTemplateField::systemFieldDefinitions();
    }

    public function getTemplatesProperty()
    {
        $query = CensusTemplate::with('creator', 'fields')
            ->where('is_archived', false);

        if (!empty(trim($this->search))) {
            $searchTerm = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhere('description', 'like', $searchTerm);
            });
        }

        if ($this->filterType === 'default') {
            $query->where('is_default', true);
        } elseif ($this->filterType === 'custom') {
            $query->where('is_default', false);
        }

        return $query->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function showCreate(): void
    {
        $this->resetForm();
        $this->mode = 'create';
    }

    public function showEdit(string $id): void
    {
        $this->resetForm();
        $this->mode = 'edit';
        $this->templateId = $id;

        $template = CensusTemplate::with('fields')->findOrFail($id);
        $this->name = $template->name;
        $this->description = $template->description ?? '';
        $this->is_default = (bool) $template->is_default;

        foreach ($template->fields as $field) {
            $this->fields[] = [
                'id'                => $field->id,
                'group_name'        => $field->group_name,
                'field_key'         => $field->field_key,
                'field_label'       => $field->field_label,
                'field_type'        => $field->field_type,
                'field_options'     => is_array($field->field_options) ? implode(', ', $field->field_options) : '',
                'is_required'       => (bool) $field->is_required,
                'is_system_field'   => (bool) $field->is_system_field,
                'profile_field_key' => $field->profile_field_key,
                'placeholder_text'  => $field->placeholder_text ?? '',
                'help_text'         => $field->help_text ?? '',
            ];
        }
    }

    public function cancel(): void
    {
        $this->resetForm();
        $this->mode = 'list';
    }

    private function resetForm(): void
    {
        $this->templateId = null;
        $this->name = '';
        $this->description = '';
        $this->is_default = false;
        $this->fields = [];
        $this->selectedSystemField = '';
        $this->resetValidation();
    }

    public function addSystemField(): void
    {
        if (empty($this->selectedSystemField)) {
            return;
        }

        $this->addSystemFieldByKey($this->selectedSystemField);
        $this->selectedSystemField = '';
    }

    private function addSystemFieldByKey(string $key): void
    {
        // Check if already added
        foreach ($this->fields as $f) {
            if ($f['field_key'] === $key) {
                $this->addError('selectedSystemField', "Field '{$key}' sudah ditambahkan.");
                return;
            }
        }

        $definitions = CensusTemplateField::systemFieldDefinitions();
        $found = null;
        foreach ($definitions as $def) {
            if ($def['key'] === $key) {
                $found = $def;
                break;
            }
        }

        if ($found) {
            $this->fields[] = [
                'id'                => null,
                'group_name'        => $found['group'],
                'field_key'         => $found['key'],
                'field_label'       => $found['label'],
                'field_type'        => $found['type'],
                'field_options'     => is_array($found['options']) ? implode(', ', $found['options']) : '',
                'is_required'       => (bool) $found['required'],
                'is_system_field'   => true,
                'profile_field_key' => $found['profile_key'],
                'placeholder_text'  => '',
                'help_text'         => '',
            ];
        }
    }

    public function addCustomField(): void
    {
        $this->fields[] = [
            'id'                => null,
            'group_name'        => 'Data Tambahan',
            'field_key'         => 'field_' . count($this->fields),
            'field_label'       => 'Kolom Baru ' . (count($this->fields) + 1),
            'field_type'        => 'text',
            'field_options'     => '',
            'is_required'       => false,
            'is_system_field'   => false,
            'profile_field_key' => null,
            'placeholder_text'  => '',
            'help_text'         => '',
        ];
    }

    public function removeField(int $index): void
    {
        if (isset($this->fields[$index])) {
            unset($this->fields[$index]);
            $this->fields = array_values($this->fields); // re-index
        }
    }

    public function moveUp(int $index): void
    {
        if ($index > 0 && isset($this->fields[$index])) {
            $temp = $this->fields[$index - 1];
            $this->fields[$index - 1] = $this->fields[$index];
            $this->fields[$index] = $temp;
        }
    }

    public function moveDown(int $index): void
    {
        if ($index < count($this->fields) - 1 && isset($this->fields[$index])) {
            $temp = $this->fields[$index + 1];
            $this->fields[$index + 1] = $this->fields[$index];
            $this->fields[$index] = $temp;
        }
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'fields' => 'required|array|min:1',
            'fields.*.field_key' => 'required|alpha_dash|max:50',
            'fields.*.field_label' => 'required|string|max:100',
            'fields.*.field_type' => 'required|in:text,textarea,dropdown,boolean,number,date',
        ], [
            'fields.required' => 'Template harus memiliki minimal satu field.',
            'fields.*.field_key.required' => 'Key wajib diisi.',
            'fields.*.field_key.alpha_dash' => 'Key hanya boleh berisi huruf, angka, strip, dan underscore.',
            'fields.*.field_label.required' => 'Label wajib diisi.',
        ]);

        // Validate key uniqueness within the template and dropdown options
        $keys = [];
        foreach ($this->fields as $idx => $f) {
            $key = $f['field_key'];
            if (in_array($key, $keys)) {
                $this->addError("fields.{$idx}.field_key", "Key '{$key}' duplikat dalam template ini.");
                return;
            }
            $keys[] = $key;

            if ($f['field_type'] === 'dropdown' && empty(trim($f['field_options'] ?? ''))) {
                $this->addError("fields.{$idx}.field_options", "Pilihan Opsi wajib diisi untuk tipe Pilihan Ganda.");
                return;
            }
        }

        // Format fields for the service
        $formattedFields = [];
        foreach ($this->fields as $idx => $f) {
            // Parse options for dropdown
            $options = null;
            if ($f['field_type'] === 'dropdown' && !empty($f['field_options'])) {
                $options = array_map('trim', explode(',', $f['field_options']));
            }

            $formattedFields[] = [
                'group_name'       => $f['group_name'] ?: 'Umum',
                'field_key'        => $f['field_key'],
                'field_label'      => $f['field_label'],
                'field_type'       => $f['field_type'],
                'field_options'    => $options,
                'is_required'      => (bool) $f['is_required'],
                'is_system_field'  => (bool) $f['is_system_field'],
                'profile_field_key'=> $f['profile_field_key'],
                'placeholder_text' => $f['placeholder_text'] ?: null,
                'help_text'        => $f['help_text'] ?: null,
                'sort_order'       => $idx + 1,
            ];
        }

        try {
            if ($this->mode === 'create') {
                $this->censusService->createTemplate([
                    'name'        => $this->name,
                    'description' => $this->description,
                    'is_default'  => $this->is_default,
                ], $formattedFields, auth()->id());
                
                $this->toastSuccess('Template sensus berhasil dibuat.');
            } else {
                $this->censusService->updateTemplate($this->templateId, [
                    'name'        => $this->name,
                    'description' => $this->description,
                    'is_default'  => $this->is_default,
                ], $formattedFields);

                $this->toastSuccess('Template sensus berhasil diperbarui.');
            }

            $this->cancel();
        } catch (\Exception $e) {
            $this->addError('name', 'Gagal menyimpan template: ' . $e->getMessage());
        }
    }

    public function confirmArchive(string $id): void
    {
        $this->confirmingArchiveId = $id;
    }

    public function cancelArchive(): void
    {
        $this->confirmingArchiveId = null;
    }

    public function archiveTemplate(): void
    {
        if (!$this->confirmingArchiveId) {
            return;
        }

        try {
            $template = CensusTemplate::findOrFail($this->confirmingArchiveId);
            $template->update(['is_archived' => true]);
            
            $this->toastSuccess('Template sensus berhasil diarsipkan.');
        } catch (\Exception $e) {
            $this->toastError('Gagal mengarsipkan template: ' . $e->getMessage());
        } finally {
            $this->confirmingArchiveId = null;
        }
    }

    public function duplicateTemplate(string $id): void
    {
        try {
            DB::transaction(function () use ($id) {
                $source = CensusTemplate::with('fields')->findOrFail($id);
                
                $clone = CensusTemplate::create([
                    'id'          => \Illuminate\Support\Str::uuid()->toString(),
                    'name'        => $source->name . ' (Salinan)',
                    'description' => $source->description,
                    'is_default'  => false,
                    'is_archived' => false,
                    'created_by'  => auth()->id(),
                ]);

                foreach ($source->fields as $field) {
                    CensusTemplateField::create([
                        'id'                => \Illuminate\Support\Str::uuid()->toString(),
                        'template_id'       => $clone->id,
                        'group_name'        => $field->group_name,
                        'field_key'         => $field->field_key,
                        'field_label'       => $field->field_label,
                        'field_type'        => $field->field_type,
                        'field_options'     => $field->field_options,
                        'placeholder_text'  => $field->placeholder_text,
                        'help_text'         => $field->help_text,
                        'is_required'       => $field->is_required,
                        'is_system_field'   => $field->is_system_field,
                        'profile_field_key' => $field->profile_field_key,
                        'sort_order'        => $field->sort_order,
                    ]);
                }
            });

            $this->toastSuccess('Template sensus berhasil diduplikasi.');
        } catch (\Exception $e) {
            $this->toastError('Gagal menduplikasi template: ' . $e->getMessage());
        }
    }

    // -------------------------------------------------------------------------
    // Excel Preview & Download Methods
    // -------------------------------------------------------------------------

    public function openPreviewModal(string $id): void
    {
        $this->previewTemplateId = $id;
        $this->previewActiveTab = 'simulator';
    }

    public function closePreviewModal(): void
    {
        $this->previewTemplateId = null;
    }

    public function getPreviewTemplateProperty()
    {
        if (!$this->previewTemplateId) {
            return null;
        }
        return CensusTemplate::with('fields')->find($this->previewTemplateId);
    }

    public function getPreviewRowsProperty(): array
    {
        $template = $this->previewTemplate;
        if (!$template) {
            return [];
        }

        $fields = $template->fields;
        $dummies = [
            [
                'id' => 'sample-uuid-1',
                'name' => 'Muhammad Yusuf',
                'room' => 'Umar Bin Khattab - 01',
                'enrollment' => 'aktif',
                'presence' => 'mukim',
            ],
            [
                'id' => 'sample-uuid-2',
                'name' => 'Ahmad Ibrahim',
                'room' => 'Umar Bin Khattab - 02',
                'enrollment' => 'aktif',
                'presence' => 'laju',
            ],
            [
                'id' => 'sample-uuid-3',
                'name' => 'Zainab Putri',
                'room' => 'Aisyah - 05',
                'enrollment' => 'aktif',
                'presence' => 'izin',
            ]
        ];

        $rows = [];
        foreach ($dummies as $index => $dummy) {
            $row = [
                'id' => $dummy['id'],
                'name' => $dummy['name'],
                'room' => $dummy['room'],
                'enrollment' => $dummy['enrollment'],
                'presence' => $dummy['presence'],
                'custom' => []
            ];

            foreach ($fields as $field) {
                $val = '';
                if ($field->field_type === 'boolean') {
                    $val = ($index % 2 === 0) ? 'YA' : 'TIDAK';
                } elseif ($field->field_type === 'dropdown' && !empty($field->field_options)) {
                    $opts = $field->field_options;
                    $val = $opts[$index % count($opts)] ?? '';
                } elseif ($field->field_type === 'number') {
                    $val = 10 + $index;
                } elseif ($field->field_type === 'date') {
                    $val = date('Y-m-d');
                } else {
                    $val = 'Contoh Isian ' . ($index + 1);
                }
                $row['custom'][$field->field_key] = $val;
            }
            $rows[] = $row;
        }

        return $rows;
    }

    public function downloadSampleExcel(string $templateId)
    {
        try {
            $template = CensusTemplate::with('fields')->findOrFail($templateId);
            $filename = 'Contoh_Excel_Sensus_' . str_replace(' ', '_', $template->name) . '.xlsx';
            
            return Excel::download(
                new CensusTemplateSampleExport($template),
                $filename
            );
        } catch (\Exception $e) {
            $this->toastError('Gagal mengunduh Excel contoh: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.kepengasuhan.census-template-manager', [
            'templates' => $this->templates,
        ])->layout('layouts.app', ['title' => 'Template Sensus']);
    }
}
