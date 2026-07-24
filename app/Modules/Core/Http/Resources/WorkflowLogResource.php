<?php

namespace App\Modules\Core\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkflowLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'step_order' => $this->step_order,
            'action'     => $this->action,
            'actor'      => $this->whenLoaded('actor', fn() => [
                'id'   => $this->actor->id,
                'name' => $this->actor->name,
            ]),
            'notes'      => $this->notes,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
