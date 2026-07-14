<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SmokeTest extends TestCase
{
    /**
     * A basic test to ensure the application starts and redirects to login.
     */
    public function test_the_application_redirects_to_login(): void
    {
        $response = $this->get('/');

        // Assert it redirects to /login
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /**
     * A basic test to ensure the database connection is alive.
     */
    public function test_database_connection_is_alive(): void
    {
        // Execute a simple query to ensure DB is connected
        $result = DB::select('SELECT 1');
        $this->assertNotEmpty($result);
    }
}
