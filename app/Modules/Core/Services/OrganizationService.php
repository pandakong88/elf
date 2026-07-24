<?php

namespace App\Modules\Core\Services;

use App\Modules\Core\Models\Organization;
use Illuminate\Database\Eloquent\Collection;
use DomainException;

class OrganizationService
{
    /**
     * Buat unit organisasi baru.
     */
    public function create(array $data): Organization
    {
        if (isset($data['slug'])) {
            $exists = Organization::where('slug', $data['slug'])->exists();
            if ($exists) {
                throw new DomainException("Slug '{$data['slug']}' sudah digunakan.");
            }
        }

        return Organization::create($data);
    }

    /**
     * Update data organisasi.
     */
    public function update(Organization $organization, array $data): Organization
    {
        if (isset($data['slug']) && $data['slug'] !== $organization->slug) {
            $exists = Organization::where('slug', $data['slug'])
                                  ->where('id', '!=', $organization->id)
                                  ->exists();
            if ($exists) {
                throw new DomainException("Slug '{$data['slug']}' sudah digunakan.");
            }
        }

        $organization->update($data);

        return $organization->fresh();
    }

    /**
     * Ambil semua root organizations (tanpa parent) beserta semua anaknya.
     */
    public function getTree(): Collection
    {
        return Organization::active()
                           ->roots()
                           ->with('allChildren')
                           ->get();
    }

    /**
     * Ambil daftar langsung anak dari sebuah organisasi.
     */
    public function getChildren(string $parentId): Collection
    {
        return Organization::active()
                           ->childrenOf($parentId)
                           ->get();
    }

    /**
     * Ambil semua organisasi berdasarkan tipe.
     */
    public function getByType(string|array $type): Collection
    {
        return Organization::active()
                           ->byType($type)
                           ->get();
    }

    /**
     * Ambil satu organisasi root (root = seluruh pondok).
     *
     * @throws DomainException jika tidak ada root yang ditemukan.
     */
    public function getRootPondok(): Organization
    {
        $root = Organization::active()->roots()->byType('pondok')->first();

        if (! $root) {
            throw new DomainException('Root pondok belum dikonfigurasi di database.');
        }

        return $root;
    }

    /**
     * Ambil semua ID organisasi dalam satu subtree (termasuk dirinya sendiri).
     * Berguna untuk filter laporan multi-unit.
     */
    public function getSubtreeIds(string $organizationId): array
    {
        $org = Organization::with('allChildren')->findOrFail($organizationId);

        return $this->collectIds($org);
    }

    /**
     * Rekursif kumpulkan semua ID dalam subtree.
     */
    private function collectIds(Organization $org): array
    {
        $ids = [$org->id];
        foreach ($org->children as $child) {
            $ids = array_merge($ids, $this->collectIds($child));
        }

        return $ids;
    }
}
