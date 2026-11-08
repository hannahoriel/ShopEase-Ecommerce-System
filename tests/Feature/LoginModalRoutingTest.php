<?php

namespace Tests\Feature;

use Tests\TestCase;

class LoginModalRoutingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['session.driver' => 'array']);
    }

    public function test_direct_login_page_redirects_to_landing_login_modal(): void
    {
        $this->get('/auth/login')->assertRedirect(route('landing.page', ['auth' => 'login']));
    }

    public function test_login_form_remains_available_inside_landing_modal(): void
    {
        $this->get('/auth/login?modal=1')->assertOk()->assertSee('action="'.route('login.attempt').'"', false);
        $this->withHeader('Sec-Fetch-Dest', 'iframe')->get('/auth/login')->assertOk()->assertSee('action="'.route('login.attempt').'"', false);
    }

    public function test_guest_account_access_uses_landing_login_modal(): void
    {
        $this->get('/admin/dashboard')->assertRedirect(route('landing.page', ['auth' => 'login']));
        $this->get('/logistics/dashboard')->assertRedirect(route('logistics.landing.info'));
    }
}
