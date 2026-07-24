<?php

namespace App\Modules\Kepengasuhan\Http\Controllers;

use App\Modules\Kepengasuhan\Http\Resources\DormitoryResource;
use App\Modules\Kepengasuhan\Http\Resources\PersonResource;
use App\Modules\Kepengasuhan\Http\Resources\RoomAssignmentResource;
use App\Modules\Kepengasuhan\Http\Resources\RoomResource;
use App\Modules\Kepengasuhan\Models\Dormitory;
use App\Modules\Kepengasuhan\Models\Room;
use App\Modules\Kepengasuhan\Services\DormitoryService;
use DomainException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;

class DormitoryController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private readonly DormitoryService $dormitoryService
    ) {}

    /**
     * GET /api/v1/kepengasuhan/dormitories
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Dormitory::class);

        $dormitories = Dormitory::active()
            ->when($request->filled('organization_id'), fn($q) => $q->where('organization_id', $request->organization_id))
            ->when($request->filled('gender'), fn($q) => $q->gender($request->gender))
            ->with(['organization', 'rooms'])
            ->get();

        return DormitoryResource::collection($dormitories);
    }

    /**
     * POST /api/v1/kepengasuhan/dormitories
     */
    public function store(Request $request): DormitoryResource
    {
        $this->authorize('create', Dormitory::class);

        $validated = $request->validate([
            'organization_id' => 'required|uuid|exists:organizations,id',
            'name'            => 'required|string|max:255',
            'gender'          => 'required|in:L,P',
            'description'     => 'nullable|string',
            'is_active'       => 'boolean',
        ]);

        $dormitory = $this->dormitoryService->createDormitory($validated);

        return new DormitoryResource($dormitory);
    }

    /**
     * GET /api/v1/kepengasuhan/dormitories/{dormitory}
     */
    public function show(Dormitory $dormitory): DormitoryResource
    {
        $this->authorize('view', [Dormitory::class, $dormitory->organization_id]);

        $dormitory->load(['organization', 'rooms.currentAssignments.person']);

        return new DormitoryResource($dormitory);
    }

    /**
     * POST /api/v1/kepengasuhan/dormitories/{dormitory}/rooms
     */
    public function storeRoom(Request $request, Dormitory $dormitory): RoomResource
    {
        $this->authorize('update', [Dormitory::class, $dormitory->organization_id]);

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'capacity'    => 'required|integer|min:1',
            'description' => 'nullable|string',
            'is_active'   => 'boolean',
        ]);

        $room = $this->dormitoryService->createRoom($dormitory->id, $validated);

        return new RoomResource($room);
    }

    /**
     * POST /api/v1/kepengasuhan/rooms/{room}/assign
     */
    public function assignRoom(Request $request, Room $room): RoomAssignmentResource|JsonResponse
    {
        $this->authorize('update', [Dormitory::class, $room->dormitory->organization_id]);

        $validated = $request->validate([
            'person_id'  => 'required|uuid|exists:persons,id',
            'valid_from' => 'required|date',
            'valid_until'=> 'nullable|date|after_or_equal:valid_from',
        ]);

        try {
            $assignment = $this->dormitoryService->assignRoom(
                $room->id,
                $validated['person_id'],
                $validated['valid_from'],
                $validated['valid_until'] ?? null
            );
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return new RoomAssignmentResource($assignment->load(['room', 'person']));
    }

    /**
     * GET /api/v1/kepengasuhan/rooms/{room}/occupants
     */
    public function occupants(Room $room): AnonymousResourceCollection
    {
        $this->authorize('view', [Dormitory::class, $room->dormitory->organization_id]);

        $occupants = $this->dormitoryService->getCurrentOccupants($room->id);

        return PersonResource::collection($occupants);
    }
}
