<?php

namespace Tests\Feature;

use Tests\TestCase;

class AdminDashboardPageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_admin_dashboard_page_loads(): void
    {
        $response = $this->get('/admin');

        $response->assertOk();
        $response->assertSee('NextScout');
        $response->assertSee('Dashboard');
        $response->assertSee('Load Dashboard');
    }
}
