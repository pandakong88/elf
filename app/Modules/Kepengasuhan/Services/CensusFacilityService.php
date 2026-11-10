<?php

namespace App\Modules\Kepengasuhan\Services;

use App\Modules\Kepengasuhan\Models\CensusFacilityItem;
use App\Modules\Kepengasuhan\Models\CensusV3CampaignDormitory;
use App\Modules\Kepengasuhan\Models\Dormitory;
use App\Modules\Kepengasuhan\Models\Locker;
use App\Modules\Kepengasuhan\Models\Room;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use DomainException;

class CensusFacilityService
{
    public function __construct(
        protected LockerService $lockerService,
        protected CensusV3Service $censusV3Service
    ) {}

    /**
     * Item standar fasilitas komplek.
     */
    public const DEFAULT_KOMPLEK_ITEMS = [
        'Pintu Utama Komplek',
        'Lampu Koridor & Lorong',
        'Tempat Wudhu & Keran Air',
        'Kamar Mandi & WC Komplek',
        'Tempat Jemuran & Rak Sandal',
    ];

    /**
     * Item standar fasilitas kamar.
     */
    public const DEFAULT_KAMAR_ITEMS = [
        'Pintu Kamar',
        'Jendela & Kaca',
        'Lampu Penerangan',
        'Kipas Angin / Ventilasi',
        'Stop Kontak Listrik',
    ];

    /**
     * Ambil struktur pohon fasilitas lengkap untuk asrama tertentu (Komplek -> Kamar -> Lemari).
     */
    public function getFacilityCensusSheet(string $dormitoryId): array
    {
        $dormitory = Dormitory::with(['rooms' => function ($q) {
            $q->where('is_active', true)->with(['lockers.assignedPerson.santriProfile']);
        }])->findOrFail($dormitoryId);

        $roomsData = [];
        foreach ($dormitory->rooms as $room) {
            $roomsData[] = [
                'id'          => $room->id,
                'name'        => $room->name,
                'capacity'    => $room->capacity,
                'kamar_items' => self::DEFAULT_KAMAR_ITEMS,
                'lockers'     => $room->lockers,
            ];
        }

        return [
            'dormitory'     => $dormitory,
            'komplek_items' => self::DEFAULT_KOMPLEK_ITEMS,
            'rooms'         => $roomsData,
        ];
    }

    /**
     * Ambil data isian fasilitas yang sudah tersimpan untuk kampanye & asrama tertentu.
     */
    public function getSavedEntries(string $campaignId, string $dormitoryId): Collection
    {
        return CensusFacilityItem::where('campaign_id', $campaignId)
            ->where('dormitory_id', $dormitoryId)
            ->get();
    }

    /**
     * Simpan entri kondisi fasilitas.
     */
    public function saveFacilityEntry(
        string $campaignId,
        string $dormitoryId,
        array  $data,
        string $userId
    ): CensusFacilityItem {
        $cd = CensusV3CampaignDormitory::where('campaign_id', $campaignId)
            ->where('dormitory_id', $dormitoryId)
            ->firstOrFail();

        if (!in_array($cd->status, ['pending', 'in_progress', 'rejected'])) {
            throw new DomainException('Laporan sensus fasilitas asrama ini sudah dikirim atau disetujui, dan tidak dapat diubah lagi.');
        }

        // Kunci unik berdasarkan item_type dan locker_id atau item_name
        $match = [
            'campaign_id'  => $campaignId,
            'dormitory_id' => $dormitoryId,
            'item_type'    => $data['item_type'],
        ];

        if ($data['item_type'] === 'lemari') {
            $match['locker_id'] = $data['locker_id'];
        } elseif ($data['item_type'] === 'kamar') {
            $match['room_id']   = $data['room_id'];
            $match['item_name'] = $data['item_name'];
        } else {
            // komplek
            $match['item_name'] = $data['item_name'];
        }

        $entry = CensusFacilityItem::updateOrCreate(
            $match,
            [
                'room_id'            => $data['room_id'] ?? null,
                'locker_id'          => $data['locker_id'] ?? null,
                'item_name'          => $data['item_name'],
                'condition'          => $data['condition'] ?? 'baik',
                'assigned_person_id' => $data['assigned_person_id'] ?? null,
                'notes'              => $data['notes'] ?? null,
                'entered_by'         => $userId,
                'apply_status'       => 'pending',
                'apply_error'        => null,
            ]
        );

        if ($cd->status === 'pending') {
            $cd->update(['status' => 'in_progress']);
        }

        return $entry;
    }

    /**
     * Terapkan persetujuan sensus fasilitas:
     * Update kondisi dan pemilik lemari di tabel `lockers`.
     */
    public function applyApprovedFacilities(string $campaignDormitoryId, string $approvedBy): array
    {
        $cd = CensusV3CampaignDormitory::with('campaign', 'dormitory')->findOrFail($campaignDormitoryId);

        if ($cd->status !== 'submitted') {
            throw new DomainException('Hanya laporan berstatus submitted yang bisa disetujui.');
        }

        $items = CensusFacilityItem::where('campaign_id', $cd->campaign_id)
            ->where('dormitory_id', $cd->dormitory_id)
            ->get();

        $appliedCount = 0;
        $failedCount  = 0;
        $errors       = [];

        foreach ($items as $item) {
            try {
                if ($item->item_type === 'lemari' && $item->locker_id) {
                    $locker = Locker::find($item->locker_id);
                    if ($locker) {
                        $locker->update([
                            'condition'          => $item->condition,
                            'assigned_person_id' => $item->assigned_person_id ?? $locker->assigned_person_id,
                            'notes'              => $item->notes ?? $locker->notes,
                        ]);
                    }
                }

                $item->update([
                    'apply_status' => 'applied',
                    'apply_error'  => null,
                ]);
                $appliedCount++;
            } catch (\Exception $e) {
                $item->update([
                    'apply_status' => 'failed',
                    'apply_error'  => $e->getMessage(),
                ]);
                $failedCount++;
                $errors[] = "Gagal memperbarui {$item->item_name}: " . $e->getMessage();
            }
        }

        // Setujui status asrama di kampanye sensus
        $this->censusV3Service->approveDormitory($cd->id, $approvedBy);

        return [
            'total'   => $items->count(),
            'applied' => $appliedCount,
            'failed'  => $failedCount,
            'errors'  => $errors,
        ];
    }
}
