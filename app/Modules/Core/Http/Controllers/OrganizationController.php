<?php

namespace App\Modules\Core\Http\Controllers;

use App\Modules\Core\Http\Resources\OrganizationResource;
use App\Modules\Core\Models\Organization;
use App\Modules\Core\Services\OrganizationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use DomainException;

class OrganizationController extends Controller
{
    public function __construct(
        private readonly OrganizationService $organizationService
    ) {}

    /**
     * GET /api/v1/core/organizations
     * Daftar flat semua organisasi. Bisa difilter by type.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Organization::active();

        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        return OrganizationResource::collection($query->get());
    }

    /**
     * GET /api/v1/core/organizations/tree
     * Hierarki lengkap dari root.
     */
    public function tree(): AnonymousResourceCollection
    {
        $tree = $this->organizationService->getTree();

        return OrganizationResource::collection($tree);
    }

    /**
     * GET /api/v1/core/organizations/{organization}
     */
    public function show(Organization $organization): OrganizationResource
    {
        $organization->load(['parent', 'children', 'positions']);

        return new OrganizationResource($organization);
    }

    /**
     * POST /api/v1/core/organizations
     */
    public function store(Request $request): OrganizationResource|JsonResponse
    {
        $validated = $request->validate([
            'parent_id'   => 'nullable|uuid|exists:organizations,id',
            'name'        => 'required|string|max:255',
            'slug'        => 'required|string|max:100|unique:organizations,slug',
            'type'        => 'required|in:pondok,unit,madrasah,koperasi,tahfidz,lainnya',
            'description' => 'nullable|string',
            'is_active'   => 'boolean',
        ]);

        try {
            $org = $this->organizationService->create($validated);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return new OrganizationResource($org);
    }

    /**
     * PUT /api/v1/core/organizations/{organization}
     */
    public function update(Request $request, Organization $organization): OrganizationResource|JsonResponse
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'slug'        => 'required|string|max:100|unique:organizations,slug,' . $organization->id,
            'type'        => 'required|in:pondok,unit,madrasah,koperasi,tahfidz,lainnya',
            'description' => 'nullable|string',
            'is_active'   => 'boolean',
        ]);

        try {
            $org = $this->organizationService->update($organization, $validated);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return new OrganizationResource($org);
    }
}
