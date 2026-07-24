<?php

namespace App\Modules\Core\Http\Controllers;

use App\Modules\Core\Http\Resources\MasterDataResource;
use App\Modules\Core\Models\MasterData;
use App\Modules\Shared\MasterData\MasterDataService;
use DomainException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;

class MasterDataController extends Controller
{
    public function __construct(
        private readonly MasterDataService $masterDataService
    ) {}

    /**
     * GET /api/v1/core/master-data?category=jenis_izin&organization_id=...
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'category'        => 'required|string',
            'organization_id' => 'nullable|uuid|exists:organizations,id',
        ]);

        $data = $this->masterDataService->getByCategory(
            $request->category,
            $request->organization_id
        );

        return MasterDataResource::collection($data);
    }

    /**
     * POST /api/v1/core/master-data
     */
    public function store(Request $request): MasterDataResource|JsonResponse
    {
        $validated = $request->validate([
            'organization_id' => 'nullable|uuid|exists:organizations,id',
            'category'        => 'required|string|max:100',
            'code'            => 'required|string|max:50',
            'name'            => 'required|string|max:255',
            'description'     => 'nullable|string',
            'metadata'        => 'nullable|array',
            'sort_order'      => 'nullable|integer|min:0',
        ]);

        try {
            $masterData = $this->masterDataService->create($validated);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return new MasterDataResource($masterData);
    }

    /**
     * PUT /api/v1/core/master-data/{masterData}
     */
    public function update(Request $request, MasterData $masterData): MasterDataResource
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'metadata'    => 'nullable|array',
            'sort_order'  => 'nullable|integer|min:0',
            'is_active'   => 'boolean',
        ]);

        $masterData = $this->masterDataService->update($masterData, $validated);

        return new MasterDataResource($masterData);
    }
}
