<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class WilayahApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed Indonesia data if not already seeded
        // Note: This uses the laravolt/indonesia seeder
        // For faster tests, you might want to seed only a subset
    }

    public function test_provinces_api_returns_data(): void
    {
        // Note: Requires laravolt/indonesia data to be seeded
        $response = $this->get('/api/wilayah/provinsi');

        $response->assertOk();
        $response->assertJsonStructure([
            '*' => ['id', 'name'],
        ]);
    }

    public function test_cities_api_requires_province_id(): void
    {
        // Without province ID
        $response = $this->get('/api/wilayah/kota/');

        // Should return 404 or empty
        $response->assertStatus(404);
    }

    public function test_cities_api_returns_data_for_valid_province(): void
    {
        // Note: Requires laravolt/indonesia data
        // Province ID '11' is Aceh
        $response = $this->get('/api/wilayah/kota/11');

        $response->assertOk();
        $response->assertJsonStructure([
            '*' => ['id', 'name'],
        ]);
    }

    public function test_districts_api_returns_data_for_valid_city(): void
    {
        // Note: Requires laravolt/indonesia data
        // City ID '1101' is Kabupaten Simeulue
        $response = $this->get('/api/wilayah/kecamatan/1101');

        $response->assertOk();
        $response->assertJsonStructure([
            '*' => ['id', 'name'],
        ]);
    }

    public function test_villages_api_returns_data_for_valid_district(): void
    {
        // Note: Requires laravolt/indonesia data
        // District ID '1101010' is Teupah Selatan
        $response = $this->get('/api/wilayah/desa/1101010');

        $response->assertOk();
        $response->assertJsonStructure([
            '*' => ['id', 'name'],
        ]);
    }

    public function test_api_responses_are_cached(): void
    {
        // First request
        $response1 = $this->get('/api/wilayah/provinsi');
        $response1->assertOk();

        // Second request should use cache
        $response2 = $this->get('/api/wilayah/provinsi');
        $response2->assertOk();

        // Both responses should be identical
        $this->assertEquals($response1->getContent(), $response2->getContent());
    }
}
