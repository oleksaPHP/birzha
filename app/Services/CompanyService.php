<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Company;
use App\Models\CompanyVersion;
use Illuminate\Database\DatabaseManager;

class CompanyService
{
    public function __construct(
        private readonly DatabaseManager $db,
    ) {
    }

    /**
     * @param array{name:string,edrpou:string,address:string} $data
     * @return array{status:string,company_id:int,version:int}
     */
    public function upsert(array $data): array
    {
        return $this->db->transaction(function () use ($data): array {
            $company = Company::query()
                ->where('edrpou', $data['edrpou'])
                ->lockForUpdate()
                ->first();

            if ($company === null) {
                $company = Company::query()->create($data);

                $this->createVersion(
                    company: $company,
                    version: 1,
                    snapshot: $data,
                    oldData: null,
                    newData: $data,
                );

                return [
                    'status' => 'created',
                    'company_id' => (int) $company->id,
                    'version' => 1,
                ];
            }

            $currentData = [
                'name' => $company->name,
                'edrpou' => $company->edrpou,
                'address' => $company->address,
            ];

            if ($currentData === $data) {
                return [
                    'status' => 'duplicate',
                    'company_id' => (int) $company->id,
                    'version' => (int) $company->versions()->max('version'),
                ];
            }

            $company->update($data);

            $version = (int) $company->versions()->max('version') + 1;

            $this->createVersion(
                company: $company,
                version: $version,
                snapshot: $data,
                oldData: $currentData,
                newData: $data,
            );

            return [
                'status' => 'updated',
                'company_id' => (int) $company->id,
                'version' => $version,
            ];
        });
    }

    /**
     * @param array{name:string,edrpou:string,address:string} $snapshot
     * @param array<string, mixed>|null $oldData
     * @param array<string, mixed> $newData
     */
    private function createVersion(
        Company $company,
        int $version,
        array $snapshot,
        ?array $oldData,
        array $newData,
    ): void {
        CompanyVersion::query()->create([
            'company_id' => $company->id,
            'version' => $version,
            'name' => $snapshot['name'],
            'edrpou' => $snapshot['edrpou'],
            'address' => $snapshot['address'],
            'old_data' => $oldData,
            'new_data' => $newData,
        ]);
    }
}
