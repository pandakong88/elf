<?php

namespace App\Modules\Core\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkflowInstanceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'entity_type'  => $this->entity_type,
            'entity_id'    => $this->entity_id,
            'current_step' => $this->current_step,
            'status'       => $this->status,
            'template'     => $this->whenLoaded('template', fn() => [
                'id'          => $this->template->id,
                'name'        => $this->template->name,
                'entity_type' => $this->template->entity_type,
            ]),
            'initiator'    => $this->whenLoaded('initiator', fn() => [
                'id'   => $this->initiator->id,
                'name' => $this->initiator->name,
            ]),
            'logs'         => WorkflowLogResource::collection($this->whenLoaded('logs')),
            'created_at'   => $this->created_at?->toIso8601String(),
            'updated_at'   => $this->updated_at?->toIso8601String(),
        ];
    }
}
