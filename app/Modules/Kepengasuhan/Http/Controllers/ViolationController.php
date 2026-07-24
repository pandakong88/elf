<?php

namespace App\Modules\Kepengasuhan\Http\Controllers;

use App\Modules\Core\Models\Person;
use App\Modules\Kepengasuhan\Http\Resources\ViolationResource;
use App\Modules\Kepengasuhan\Models\Violation;
use App\Modules\Kepengasuhan\Services\ViolationService;
use DomainException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;

class ViolationController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private readonly ViolationService $violationService
    ) {}

    /**
     * GET /api/v1/kepengasuhan/violations
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Violation::class);

        $query = Violation::with(['person', 'organization', 'violationType', 'reporter']);

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

        $violations = $query->paginate($request->integer('per_page', 15));

        return ViolationResource::collection($violations);
    }

    /**
     * POST /api/v1/kepengasuhan/violations
     */
    public function store(Request $request): ViolationResource|JsonResponse
    {
        $this->authorize('create', Violation::class);

        $validated = $request->validate([
            'person_id'         => 'required|uuid|exists:persons,id',
            'organization_id'   => 'required|uuid|exists:organizations,id',
            'violation_type_id' => 'required|uuid|exists:master_data,id',
            'violation_date'    => 'nullable|date',
            'description'       => 'required|string|min:5',
            'severity'          => 'required|in:ringan,sedang,berat',
            'punishment'        => 'nullable|string',
            'points'            => 'nullable|integer|min:0',
        ]);

        // Reporter adalah person yang login saat ini
        $actorUser = auth()->user();
        if (! $actorUser->person_id) {
            return response()->json(['message' => 'User login tidak terhubung dengan profil Person.'], 422);
        }
        $validated['reporter_id'] = $actorUser->person_id;

        try {
            $violation = $this->violationService->reportViolation($validated);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return new ViolationResource($violation->load(['person', 'organization', 'violationType', 'reporter']));
    }

    /**
     * GET /api/v1/kepengasuhan/violations/{violation}
     */
    public function show(Violation $violation): ViolationResource
    {
        $this->authorize('view', [Violation::class, $violation->organization_id]);

        $violation->load(['person', 'organization', 'violationType', 'reporter']);

        return new ViolationResource($violation);
    }

    /**
     * POST /api/v1/kepengasuhan/violations/{violation}/resolve
     */
    public function resolve(Request $request, Violation $violation): ViolationResource|JsonResponse
    {
        $this->authorize('resolve', [Violation::class, $violation->organization_id]);

        $validated = $request->validate([
            'punishment_applied' => 'required|string|min:5',
        ]);

        try {
            $violation = $this->violationService->resolveViolation($violation->id, $validated['punishment_applied']);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return new ViolationResource($violation->load(['person', 'organization', 'violationType', 'reporter']));
    }
}
