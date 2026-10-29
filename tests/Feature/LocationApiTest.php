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
            '/api/v1/locations/provinces/0100000000/cities',
            '/api/v1/locations/cities/0100000000/barangays',
        ] as $path) {
            $this->getJson($path)
                ->assertOk()
                ->assertExactJson([
                    ['code' => '0100000000', 'name' => 'Test Location'],
                ]);
        }

        $this->getJson('/api/v1/locations/provinces')
            ->assertOk()
            ->assertJsonFragment([
                'code' => '0100000000',
                'name' => 'Test Location',
            ])
            ->assertJsonFragment([
                'code' => '130000000',
                'name' => 'Metro Manila (NCR)',
                'isRegion' => true,
            ]);
    }

    public function test_ncr_municipality_route_uses_the_region_endpoint(): void
    {
        config(['locations.base_urls.psgc' => 'https://psgc.cloud/api']);
        config(['locations.ncr_code' => '130000000']);
        config(['cache.default' => 'array']);
        Http::fake([
            'https://psgc.cloud/api/regions/130000000/cities-municipalities' => Http::response([
                ['code' => '1339000000', 'name' => 'Manila'],
            ]),
        ]);

        $this->getJson('/api/v1/locations/provinces/130000000/cities?is_region=1')
            ->assertOk()
            ->assertExactJson([
                ['code' => '1339000000', 'name' => 'Manila'],
            ]);

        Http::assertSent(fn ($request) => $request->url() === 'https://psgc.cloud/api/regions/130000000/cities-municipalities');
    }
}
