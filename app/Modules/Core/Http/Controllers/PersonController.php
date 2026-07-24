<?php

namespace App\Modules\Core\Http\Controllers;

use App\Modules\Core\Http\Resources\PersonResource;
use App\Modules\Core\Models\Person;
use App\Modules\Core\Services\PersonService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use DomainException;

class PersonController extends Controller
{
    public function __construct(
        private readonly PersonService $personService
    ) {}

    /**
     * GET /api/v1/core/persons
     * Daftar semua person. Bisa difilter by role_type dan organization_id.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Person::with(['roles', 'currentPositions.position']);

        if ($request->filled('role')) {
            $query->byRole($request->role, $request->organization_id);
        }

        if ($request->filled('organization_id')) {
            $query->byOrganization($request->organization_id);
        }

        if ($request->filled('gender')) {
            $query->gender($request->gender);
        }

        $persons = $query->paginate($request->integer('per_page', 15));

        return PersonResource::collection($persons);
    }

    /**
     * POST /api/v1/core/persons
     */
    public function store(Request $request): PersonResource|JsonResponse
    {
        $validated = $request->validate([
            'nik'         => 'nullable|string|max:20',
            'name'        => 'required|string|max:255',
            'gender'      => 'required|in:L,P',
            'birth_place' => 'nullable|string|max:100',
            'birth_date'  => 'nullable|date',
            'phone'       => 'nullable|string|max:20',
            'address'     => 'nullable|string',
            'notes'       => 'nullable|string',
        ]);

        try {
            $person = $this->personService->create($validated);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return new PersonResource($person);
    }

    /**
     * GET /api/v1/core/persons/{person}
     */
    public function show(Person $person): PersonResource
    {
        $person->load(['roles.organization', 'currentPositions.position']);

        return new PersonResource($person);
    }

    /**
     * PUT /api/v1/core/persons/{person}
     */
    public function update(Request $request, Person $person): PersonResource|JsonResponse
    {
        $validated = $request->validate([
            'nik'         => 'nullable|string|max:20',
            'name'        => 'required|string|max:255',
            'gender'      => 'required|in:L,P',
            'birth_place' => 'nullable|string|max:100',
            'birth_date'  => 'nullable|date',
            'phone'       => 'nullable|string|max:20',
            'address'     => 'nullable|string',
            'notes'       => 'nullable|string',
        ]);

        try {
            $person = $this->personService->update($person, $validated);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return new PersonResource($person);
    }

    /**
     * DELETE /api/v1/core/persons/{person}
     * Soft delete — person tidak hilang, hanya dinonaktifkan.
     */
    public function destroy(Person $person): JsonResponse
    {
        $this->personService->deactivate($person);

        return response()->json(['message' => "Person '{$person->name}' berhasil dinonaktifkan."], 200);
    }

    /**
     * POST /api/v1/core/persons/{person}/roles
     * Assign role baru ke person.
     */
    public function assignRole(Request $request, Person $person): JsonResponse
    {
        $validated = $request->validate([
            'organization_id' => 'required|uuid|exists:organizations,id',
            'role_type'       => 'required|in:santri,wali,guru,pengurus,pegawai,umum',
            'valid_from'      => 'nullable|date',
            'valid_until'     => 'nullable|date|after:valid_from',
        ]);

        try {
            $role = $this->personService->assignRole(
                $person,
                $validated['organization_id'],
                $validated['role_type'],
                $validated['valid_from'] ?? null,
                $validated['valid_until'] ?? null
            );
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'message' => "Role '{$validated['role_type']}' berhasil diberikan ke {$person->name}.",
            'role_id' => $role->id,
        ], 201);
    }
}
