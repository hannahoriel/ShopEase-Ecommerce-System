<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_shows_the_login_page_first(): void
    {
        $response = $this->get('/');

        $response->assertRedirectToRoute('login');
    }
}
