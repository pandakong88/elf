<?php

namespace App\Modules\Shared\MasterData;

use App\Modules\Core\Models\MasterData;
use DomainException;
use Illuminate\Database\Eloquent\Collection;

class MasterDataService
{
    /**
     * Ambil semua data berdasarkan kategori.
     * Jika organizationId diberikan, sertakan juga data global.
     */
    public function getByCategory(string $category, ?string $organizationId = null): Collection
    {
        $query = MasterData::active()->byCategory($category)->ordered();

        if ($organizationId) {
            $query->forOrganization($organizationId);
        } else {
            $query->global();
        }

        return $query->get();
    }

    /**
     * Buat entry master data baru.
     *
     * @throws DomainException jika kode sudah ada di kategori dan organisasi yang sama.
     */
    public function create(array $data): MasterData
    {
        $exists = MasterData::where('category', $data['category'])
                            ->where('code', $data['code'])
                            ->where('organization_id', $data['organization_id'] ?? null)
                            ->exists();

        if ($exists) {
            throw new DomainException(
                "Kode '{$data['code']}' sudah ada di kategori '{$data['category']}'."
            );
        }

        return MasterData::create($data);
    }

    /**
     * Update entry master data.
     */
    public function update(MasterData $masterData, array $data): MasterData
    {
        $masterData->update($data);

        return $masterData->fresh();
    }

    /**
     * Nonaktifkan entry master data.
     */
    public function deactivate(MasterData $masterData): void
    {
        $masterData->update(['is_active' => false]);
    }

    /**
     * Ambil satu entry berdasarkan kategori dan kode.
     *
     * @throws DomainException jika tidak ditemukan.
     */
    public function findByCode(string $category, string $code, ?string $organizationId = null): MasterData
    {
        $query = MasterData::active()
                           ->byCategory($category)
                           ->where('code', $code);

        if ($organizationId) {
            $query->forOrganization($organizationId);
        }

        $result = $query->first();

        if (! $result) {
            throw new DomainException(
                "Master data '{$code}' di kategori '{$category}' tidak ditemukan."
            );
        }

        return $result;
    }
}
