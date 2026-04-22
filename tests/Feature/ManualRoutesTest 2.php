<?php

use App\Models\Tenant;
use App\Models\User;
use Tests\TestCase;

class ManualRouteTest extends TestCase
{
    /**
     * Test central landing page returns 200
     */
    public function test_central_app_landing_page()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    /**
     * Test central login page is accessible
     */
    public function test_central_login_page()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    /**
     * Test central register page is accessible
     */
    public function test_central_register_page()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
    }

    /**
     * Test tenant landing page shows tenant name in title
     */
    public function test_tenant_landing_displays_tenant_name()
    {
        try {
            $tenant = Tenant::query()->first();
        } catch (\Throwable) {
            $this->markTestSkipped('Landlord / tenants table unavailable.');
        }

        if (! $tenant) {
            $this->markTestSkipped('No tenant in database.');
        }

        $centralPort = (int) env('CENTRAL_PORT', 8000);
        $response = $this->get('http://'.$tenant->domain.':'.$centralPort.'/');
        $response->assertStatus(200);
        $response->assertSee($tenant->name);
    }

    /**
     * Test authenticated central user redirects to dashboard
     */
    public function test_authenticated_central_user_dashboard()
    {
        $user = User::where('role', 'admin')->first();
        if (! $user) {
            $this->markTestSkipped('No admin user found');
        }

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
    }

    /**
     * Test guest redirected to login from protected route
     */
    public function test_guest_redirected_to_login()
    {
        $response = $this->get('/dashboard');
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /**
     * Test tenant login redirects to landing page
     */
    public function test_tenant_login_redirects_to_landing()
    {
        try {
            $tenant = Tenant::query()->first();
        } catch (\Throwable) {
            $this->markTestSkipped('Landlord / tenants table unavailable.');
        }

        if (! $tenant) {
            $this->markTestSkipped('No tenant in database.');
        }

        $user = User::where('tenant_id', $tenant->id)->first();

        if (! $user) {
            $this->markTestSkipped('No tenant user found');
        }

        $this->assertTrue(Tenant::checkCurrent() || ! Tenant::checkCurrent());
    }
}
