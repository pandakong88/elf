<?php

namespace App\Modules\Core\Http\Controllers;

use App\Modules\Core\Http\Resources\WorkflowInstanceResource;
use App\Modules\Core\Models\Person;
use App\Modules\Core\Models\WorkflowInstance;
use App\Modules\Shared\Workflow\WorkflowEngine;
use DomainException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class WorkflowController extends Controller
{
    public function __construct(
        private readonly WorkflowEngine $engine
    ) {}

    /**
     * GET /api/v1/core/workflows/{instance}
     */
    public function show(WorkflowInstance $instance): WorkflowInstanceResource
    {
        $instance->load(['template.steps', 'initiator', 'logs.actor']);

        return new WorkflowInstanceResource($instance);
    }

    /**
     * POST /api/v1/core/workflows/initiate
     */
    public function initiate(Request $request): WorkflowInstanceResource|JsonResponse
    {
        $validated = $request->validate([
            'template_id'  => 'required|uuid|exists:workflow_templates,id',
            'entity_type'  => 'required|string',
            'entity_id'    => 'required|uuid',
            'initiated_by' => 'required|uuid|exists:persons,id',
        ]);

        try {
            $initiator = Person::findOrFail($validated['initiated_by']);
            $instance  = $this->engine->initiate(
                $validated['template_id'],
                $validated['entity_type'],
                $validated['entity_id'],
                $initiator
            );
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return new WorkflowInstanceResource($instance->load(['template', 'initiator']));
    }

    /**
     * POST /api/v1/core/workflows/{instance}/advance
     */
    public function advance(Request $request, WorkflowInstance $instance): WorkflowInstanceResource|JsonResponse
    {
        $validated = $request->validate([
            'actor_id' => 'required|uuid|exists:persons,id',
            'notes'    => 'nullable|string',
        ]);

        try {
            $actor    = Person::findOrFail($validated['actor_id']);
            $instance = $this->engine->advance($instance, $actor, $validated['notes'] ?? null);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return new WorkflowInstanceResource($instance->load(['template', 'logs.actor']));
    }

    /**
     * POST /api/v1/core/workflows/{instance}/reject
     */
    public function reject(Request $request, WorkflowInstance $instance): WorkflowInstanceResource|JsonResponse
    {
        $validated = $request->validate([
            'actor_id' => 'required|uuid|exists:persons,id',
            'notes'    => 'required|string|min:5',
        ]);

        try {
            $actor    = Person::findOrFail($validated['actor_id']);
            $instance = $this->engine->reject($instance, $actor, $validated['notes']);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return new WorkflowInstanceResource($instance->load(['template', 'logs.actor']));
    }
}
