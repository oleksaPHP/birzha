<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpsertCompanyRequest;
use App\Models\Company;
use App\Services\CompanyService;
use Illuminate\Http\JsonResponse;

class CompanyController extends Controller
{
    public function __construct(
        private readonly CompanyService $companyService,
    ) {
    }

    public function upsert(UpsertCompanyRequest $request): JsonResponse
    {
        return response()->json(
            $this->companyService->upsert($request->validated())
        );
    }

    public function versions(string $edrpou): JsonResponse
    {
        $company = Company::query()
            ->where('edrpou', $edrpou)
            ->firstOrFail();

        return response()->json([
            'company_id' => $company->id,
            'edrpou' => $company->edrpou,
            'versions' => $company->versions()
                ->orderByDesc('version')
                ->get([
                    'version',
                    'name',
                    'edrpou',
                    'address',
                    'old_data',
                    'new_data',
                    'created_at',
                ]),
        ]);
    }
}
