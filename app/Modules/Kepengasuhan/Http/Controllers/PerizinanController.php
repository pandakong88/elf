<?php

namespace App\Modules\Kepengasuhan\Http\Controllers;

use App\Modules\Core\Models\Person;
use App\Modules\Kepengasuhan\Http\Resources\PerizinanResource;
use App\Modules\Kepengasuhan\Models\Perizinan;
use App\Modules\Kepengasuhan\Services\PerizinanService;
use DomainException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;

class PerizinanController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private readonly PerizinanService $perizinanService
    ) {}

    /**
     * GET /api/v1/kepengasuhan/perizinan
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Perizinan::class);

        $query = Perizinan::with(['person', 'organization', 'permissionType', 'workflowInstance']);

        // Musyrif hanya bisa lihat unit asuhannya
        if (! auth()->user()->hasAnyRole(['super-admin', 'pengasuh'])) {
            $orgIds = auth()->user()->getOrganizationIds();
            $query->whereIn('organization_id', $orgIds);
        }

        if ($request->filled('person_id')) {
            $query->where('person_id', $request->person_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $perizinan = $query->paginate($request->integer('per_page', 15));

        return PerizinanResource::collection($perizinan);
    }

    /**
     * POST /api/v1/kepengasuhan/perizinan
     */
    public function store(Request $request): PerizinanResource|JsonResponse
    {
        $this->authorize('create', Perizinan::class);

        $validated = $request->validate([
            'person_id'            => 'required|uuid|exists:persons,id',
            'organization_id'      => 'required|uuid|exists:organizations,id',
            'permission_type_id'   => 'required|uuid|exists:master_data,id',
            'reason'               => 'required|string|min:5',
            'start_date'           => 'required|date',
            'end_date'             => 'required|date|after:start_date',
            'workflow_template_id' => 'required|uuid|exists:workflow_templates,id',
            'prevent_duplicate'    => 'boolean',
            'max_points_allowed'   => 'nullable|integer',
        ]);

        // Actor yang menginisiasi (mengajukan) adalah person yang terkait user yang sedang login
        $actorUser = auth()->user();
        if (! $actorUser->person_id) {
            return response()->json(['message' => 'User login tidak terhubung dengan profil Person.'], 422);
        }
        $initiator = Person::findOrFail($actorUser->person_id);

        try {
            $perizinan = $this->perizinanService->initiateLeave($validated, $initiator);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return new PerizinanResource($perizinan->load(['person', 'organization', 'permissionType', 'workflowInstance']));
    }

    /**
     * GET /api/v1/kepengasuhan/perizinan/{perizinan}
     */
    public function show(Perizinan $perizinan): PerizinanResource
    {
        $this->authorize('view', [Perizinan::class, $perizinan->organization_id, $perizinan->person_id]);

        $perizinan->load(['person', 'organization', 'permissionType', 'workflowInstance.logs.actor']);

        return new PerizinanResource($perizinan);
    }

    /**
     * POST /api/v1/kepengasuhan/perizinan/{perizinan}/checkout
     */
    public function checkout(Perizinan $perizinan): PerizinanResource|JsonResponse
    {
        $this->authorize('approve', [Perizinan::class, $perizinan->organization_id]);

        try {
            $perizinan = $this->perizinanService->checkout($perizinan->id);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return new PerizinanResource($perizinan->load(['person', 'organization', 'permissionType']));
    }

    /**
     * POST /api/v1/kepengasuhan/perizinan/{perizinan}/checkin
     */
    public function checkin(Request $request, Perizinan $perizinan): PerizinanResource|JsonResponse
    {
        $this->authorize('approve', [Perizinan::class, $perizinan->organization_id]);

        $validated = $request->validate([
            'notes' => 'nullable|string',
        ]);

        try {
            $perizinan = $this->perizinanService->checkin($perizinan->id, $validated['notes'] ?? null);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return new PerizinanResource($perizinan->load(['person', 'organization', 'permissionType']));
    }
}
