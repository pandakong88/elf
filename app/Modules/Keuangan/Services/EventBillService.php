<?php

namespace App\Modules\Keuangan\Services;

use App\Modules\Keuangan\Models\EventBill;
use App\Modules\Keuangan\Models\EventBillItem;
use App\Modules\Core\Models\Person;
use App\Modules\Kepengasuhan\Models\SantriProfile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class EventBillService
{
    /**
     * Create a new Event Bill.
     */
    public function createEventBill(string $eventName, string $eventDate, float $defaultAmount, string $createdByUserId): EventBill
    {
        return EventBill::create([
            'id' => Str::uuid()->toString(),
            'event_name' => $eventName,
            'event_date' => $eventDate,
            'default_amount' => $defaultAmount,
            'created_by' => $createdByUserId,
        ]);
    }

    /**
     * Assign event bill to all active santri.
     * Automatically applies a sibling discount if they have active siblings.
     */
    public function assignEventToAllActiveSantri(string $eventBillId, float $siblingDiscountPercent = 10.00): array
    {
        return DB::transaction(function () use ($eventBillId, $siblingDiscountPercent) {
            $eventBill = EventBill::findOrFail($eventBillId);
            $assignedCount = 0;

            // Get active santri
            $santriList = Person::whereHas('activeRoles', function ($q) {
                $q->where('role_type', 'santri')
                  ->where('enrollment_status', 'aktif');
            })->get();

            foreach ($santriList as $santri) {
                $profile = SantriProfile::where('person_id', $santri->id)->first();
                
                $discountAmount = 0.00;
                $discountReason = null;

                // Auto-detect sibling discount
                if ($profile && $profile->has_active_sibling) {
                    $discountAmount = ($siblingDiscountPercent / 100) * $eventBill->default_amount;
                    $discountReason = "Potongan Saudara Kandung ({$siblingDiscountPercent}%)";
                }

                // Check if already assigned
                $exists = EventBillItem::where('event_bill_id', $eventBillId)
                    ->where('person_id', $santri->id)
                    ->exists();

                if (!$exists) {
                    EventBillItem::create([
                        'id' => Str::uuid()->toString(),
                        'event_bill_id' => $eventBillId,
                        'person_id' => $santri->id,
                        'original_amount' => $eventBill->default_amount,
                        'discount_amount' => $discountAmount,
                        'discount_reason' => $discountReason,
                        'status' => 'unpaid',
                    ]);
                    $assignedCount++;
                }
            }

            return ['assigned' => $assignedCount];
        });
    }

    /**
     * Record payment for an event bill item.
     */
    public function recordEventPayment(string $itemId, string $status): EventBillItem
    {
        $item = EventBillItem::findOrFail($itemId);
        $item->status = $status; // 'paid' or 'waived'
        $item->save();

        return $item;
    }
}
