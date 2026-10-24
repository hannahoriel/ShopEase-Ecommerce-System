<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class LocationApiTest extends TestCase
{
    public function test_registration_location_routes_proxy_psgc_data(): void
    {
        config(['locations.base_urls.psgc' => 'https://psgc.cloud/api']);
        config(['cache.default' => 'array']);
        Http::fake([
            '*' => Http::response([
                ['code' => '0100000000', 'name' => 'Test Location'],
            ]),
        ]);

        foreach ([
            '/api/v1/locations/regions',
            '/api/v1/locations/regions/0100000000/provinces',
            '/api/v1/locations/regions/0100000000/districts',
            '/api/v1/locations/regions/0100000000/cities-municipalities',
            '/api/v1/locations/provinces/0100000000/cities-municipalities',
            '/api/v1/locations/districts/0100000000/cities-municipalities',
            '/api/v1/locations/cities-municipalities/0100000000/barangays',
        ] as $path) {
            $this->getJson($path)
                ->assertOk()
                ->assertExactJson([
                    ['code' => '0100000000', 'name' => 'Test Location'],
                ]);
        }
    }
}
