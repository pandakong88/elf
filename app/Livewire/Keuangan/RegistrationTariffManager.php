<?php

namespace App\Livewire\Keuangan;

use App\Livewire\Concerns\SendsToast;
use App\Modules\Keuangan\Models\BillingConfiguration;
use App\Modules\Madrasah\Models\MadrasahKelas;
use App\Traits\HasGenderScope;
use Illuminate\Support\Str;
use Livewire\Component;

class RegistrationTariffManager extends Component
{
    use SendsToast, HasGenderScope;

    public string $activeTab = 'items'; // 'items' (Item Pendaftaran) | 'kitab' (Tarif Kitab per Kelas)

    // Modal Add / Edit Tariff Item
    public bool    $showItemModal = false;
    public ?string $editingItemId = null;
    public string  $itemKey       = '';
    public string  $itemLabel     = '';
    public float   $itemAmount    = 0.0;
    public string  $itemCategory  = 'dasar'; // 'dasar', 'asrama', 'seragam', 'konsumsi', 'kitab'
    public string  $itemGender    = 'ALL';   // 'ALL', 'L', 'P'
    public string  $itemResidence = 'ALL';   // 'ALL', 'mukim', 'laju'
    public bool    $itemIsActive  = true;

    // Tariff Kitab Per Kelas State
    public array $kitabPrices = [];

    public function mount(): void
    {
        $this->loadKitabPrices();
    }

    public function loadKitabPrices(): void
    {
        $kelasList = MadrasahKelas::where('is_active', true)->get();
        $this->kitabPrices = [];

        foreach ($kelasList as $kelas) {
            $config = BillingConfiguration::where('type', 'kitab')
                ->where('label', 'like', "%{$kelas->name}%")
                ->first();

            $defaultPrice = 136000;
            if (str_contains(strtolower($kelas->name), 'awaliyah 2')) {
                $defaultPrice = 150000;
            } elseif (str_contains(strtolower($kelas->name), 'awaliyah 3')) {
                $defaultPrice = 175000;
            } elseif (str_contains(strtolower($kelas->name), 'wustho')) {
                $defaultPrice = 200000;
            } elseif (str_contains(strtolower($kelas->name), 'ulya')) {
                $defaultPrice = 225000;
            }

            $this->kitabPrices[$kelas->id] = [
                'kelas_id'   => $kelas->id,
                'kelas_name' => $kelas->name,
                'jenjang'    => strtoupper($kelas->jenjang),
                'amount'     => $config ? (float) $config->amount : $defaultPrice,
                'config_id'  => $config ? $config->id : null,
            ];
        }
    }

    public function saveKitabPrice(string $kelasId): void
    {
        if (!isset($this->kitabPrices[$kelasId])) return;

        $itemData = $this->kitabPrices[$kelasId];
        $kelas    = MadrasahKelas::find($kelasId);

        if (!$kelas) return;

        try {
            BillingConfiguration::updateOrCreate(
                [
                    'type'  => 'kitab',
                    'label' => "Paket Kitab {$kelas->name}",
                ],
                [
                    'id'             => $itemData['config_id'] ?: Str::uuid()->toString(),
                    'amount'         => (float) $itemData['amount'],
                    'effective_from' => now()->startOfYear(),
                    'interval'       => 'insidental',
                    'target_type'    => 'kelas',
                    'target_filters' => ['kelas_id' => $kelasId],
                    'is_active'      => true,
                    'created_by'     => auth()->id(),
                ]
            );

            $this->toastSuccess("Tarif Paket Kitab {$kelas->name} berhasil diperbarui menjadi Rp " . number_format($itemData['amount'], 0, ',', '.'));
            $this->loadKitabPrices();
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }

    public function openItemModal(?string $itemId = null): void
    {
        $this->editingItemId = $itemId;

        if ($itemId) {
            $config = BillingConfiguration::findOrFail($itemId);
            $filters = $config->target_filters ?? [];

            $this->itemLabel     = $config->label;
            $this->itemAmount    = (float) $config->amount;
            $this->itemCategory  = $filters['category'] ?? 'dasar';
            $this->itemGender    = $filters['gender'] ?? 'ALL';
            $this->itemResidence = $filters['residence'] ?? 'ALL';
            $this->itemIsActive  = (bool) $config->is_active;
        } else {
            $this->itemLabel     = '';
            $this->itemAmount    = 0.0;
            $this->itemCategory  = 'dasar';
            $this->itemGender    = 'ALL';
            $this->itemResidence = 'ALL';
            $this->itemIsActive  = true;
        }

        $this->showItemModal = true;
    }

    public function saveItem(): void
    {
        $this->validate([
            'itemLabel'  => 'required|string|max:255',
            'itemAmount' => 'required|numeric|min:0',
        ]);

        try {
            BillingConfiguration::updateOrCreate(
                ['id' => $this->editingItemId ?: Str::uuid()->toString()],
                [
                    'type'           => 'pendaftaran',
                    'label'          => $this->itemLabel,
                    'amount'         => $this->itemAmount,
                    'effective_from' => now()->startOfYear(),
                    'interval'       => 'insidental',
                    'target_type'    => 'all',
                    'target_filters' => [
                        'category'  => $this->itemCategory,
                        'gender'    => $this->itemGender,
                        'residence' => $this->itemResidence,
                    ],
                    'is_active'      => $this->itemIsActive,
                    'created_by'     => auth()->id(),
                ]
            );

            $this->toastSuccess("Tarif item {$this->itemLabel} berhasil disimpan.");
            $this->showItemModal = false;
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }

    public function toggleItemActive(string $configId): void
    {
        $config = BillingConfiguration::findOrFail($configId);
        $config->update(['is_active' => !$config->is_active]);
        $this->toastSuccess("Status keaktifan item {$config->label} diperbarui.");
    }

    public function render()
    {
        $registrationItems = BillingConfiguration::where('type', 'pendaftaran')
            ->orderBy('is_active', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.keuangan.registration-tariff-manager', [
            'registrationItems' => $registrationItems,
        ])->layout('layouts.app');
    }
}
