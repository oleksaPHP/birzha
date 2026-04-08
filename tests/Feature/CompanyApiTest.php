<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_company_and_first_version(): void
    {
        $response = $this->postJson('/api/company', [
            'name' => 'ТОВ Українська енергетична біржа',
            'edrpou' => '37027819',
            'address' => '01001, Україна, м. Київ, вул. Хрещатик, 44',
        ]);

        $response
            ->assertOk()
            ->assertJson([
                'status' => 'created',
                'company_id' => 1,
                'version' => 1,
            ]);

        $this->assertDatabaseHas('companies', [
            'id' => 1,
            'edrpou' => '37027819',
        ]);

        $this->assertDatabaseHas('company_versions', [
            'company_id' => 1,
            'version' => 1,
            'edrpou' => '37027819',
        ]);
    }

    public function test_it_returns_duplicate_when_payload_has_no_changes(): void
    {
        $payload = [
            'name' => 'ТОВ Українська енергетична біржа',
            'edrpou' => '37027819',
            'address' => '01001, Україна, м. Київ, вул. Хрещатик, 44',
        ];

        $this->postJson('/api/company', $payload)->assertOk();

        $response = $this->postJson('/api/company', $payload);

        $response
            ->assertOk()
            ->assertJson([
                'status' => 'duplicate',
                'company_id' => 1,
                'version' => 1,
            ]);

        $this->assertDatabaseCount('company_versions', 1);
    }

    public function test_it_updates_company_and_creates_new_version(): void
    {
        $this->postJson('/api/company', [
            'name' => 'ТОВ Українська енергетична біржа',
            'edrpou' => '37027819',
            'address' => '01001, Україна, м. Київ, вул. Хрещатик, 44',
        ])->assertOk();

        $response = $this->postJson('/api/company', [
            'name' => 'ТОВ Українська енергетична біржа',
            'edrpou' => '37027819',
            'address' => '01001, Україна, м. Київ, вул. Хрещатик, 44, 4 поверх',
        ]);

        $response
            ->assertOk()
            ->assertJson([
                'status' => 'updated',
                'company_id' => 1,
                'version' => 2,
            ]);

        $this->assertDatabaseHas('companies', [
            'id' => 1,
            'address' => '01001, Україна, м. Київ, вул. Хрещатик, 44, 4 поверх',
        ]);

        $this->assertDatabaseHas('company_versions', [
            'company_id' => 1,
            'version' => 2,
            'address' => '01001, Україна, м. Київ, вул. Хрещатик, 44, 4 поверх',
        ]);
    }

    public function test_it_returns_company_versions_by_edrpou(): void
    {
        $this->postJson('/api/company', [
            'name' => 'ТОВ Українська енергетична біржа',
            'edrpou' => '37027819',
            'address' => 'Адреса 1',
        ])->assertOk();

        $this->postJson('/api/company', [
            'name' => 'ТОВ Українська енергетична біржа 2',
            'edrpou' => '37027819',
            'address' => 'Адреса 2',
        ])->assertOk();

        $response = $this->getJson('/api/company/37027819/versions');

        $response
            ->assertOk()
            ->assertJsonPath('company_id', Company::query()->firstOrFail()->id)
            ->assertJsonPath('edrpou', '37027819')
            ->assertJsonCount(2, 'versions')
            ->assertJsonPath('versions.0.version', 2)
            ->assertJsonPath('versions.1.version', 1);
    }
}
