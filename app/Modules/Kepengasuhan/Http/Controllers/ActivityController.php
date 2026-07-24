<?php

namespace App\Modules\Kepengasuhan\Http\Controllers;

use App\Modules\Kepengasuhan\Http\Resources\ActivityResource;
use App\Modules\Kepengasuhan\Models\Activity;
use App\Modules\Kepengasuhan\Services\ActivityService;
use DomainException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;

class ActivityController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private readonly ActivityService $activityService
    ) {}

    /**
     * GET /api/v1/kepengasuhan/activities
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Activity::class);

        $query = Activity::with(['organization', 'activityType']);

        if (! auth()->user()->hasAnyRole(['super-admin', 'pengasuh'])) {
            $orgIds = auth()->user()->getOrganizationIds();
            $query->whereIn('organization_id', $orgIds);
        }

        if ($request->filled('organization_id')) {
            $query->where('organization_id', $request->organization_id);
        }

        if ($request->filled('date')) {
            $query->where('date', $request->date);
        }

        $activities = $query->paginate($request->integer('per_page', 15));

        return ActivityResource::collection($activities);
    }

    /**
     * POST /api/v1/kepengasuhan/activities
     */
    public function store(Request $request): ActivityResource
    {
        $this->authorize('create', Activity::class);

        $validated = $request->validate([
            'organization_id'  => 'required|uuid|exists:organizations,id',
            'activity_type_id' => 'required|uuid|exists:master_data,id',
            'name'             => 'required|string|max:255',
            'date'             => 'required|date',
            'description'      => 'nullable|string',
        ]);

        $activity = $this->activityService->createActivity($validated);

        return new ActivityResource($activity->load(['organization', 'activityType']));
    }

    /**
     * GET /api/v1/kepengasuhan/activities/{activity}
     */
    public function show(Activity $activity): ActivityResource
    {
        $this->authorize('view', [Activity::class, $activity->organization_id]);

        $activity->load(['organization', 'activityType', 'attendances.person']);

        return new ActivityResource($activity);
    }

    /**
     * POST /api/v1/kepengasuhan/activities/{activity}/attendance
     */
    public function recordAttendance(Request $request, Activity $activity): JsonResponse
    {
        $this->authorize('recordAttendance', [Activity::class, $activity->organization_id]);

        $validated = $request->validate([
            'attendances'          => 'required|array',
            'attendances.*.person_id'=> 'required|uuid|exists:persons,id',
            'attendances.*.status'   => 'required|in:hadir,izin,sakit,alfa',
            'attendances.*.notes'    => 'nullable|string|max:255',
        ]);

        try {
            $this->activityService->recordAttendanceBatch($activity->id, $validated['attendances']);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['message' => 'Absensi berhasil dicatat.']);
    }
}
