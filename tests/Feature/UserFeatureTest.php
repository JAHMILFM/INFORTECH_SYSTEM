<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class UserFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_can_access_users_index(): void
    {
        $admin = User::create([
            'name' => 'Admin', 'email' => 'admin@test.com', 'password' => 'secret', 'role' => 'SuperAdmin'
        ]);

        $response = $this->actingAs($admin)->get('/users');
        $response->assertStatus(200);
        $response->assertViewIs('users.index');
    }

    public function test_soporte_cannot_access_users_index(): void
    {
        $soporte = User::create([
            'name' => 'Soporte', 'email' => 'soporte@test.com', 'password' => 'secret', 'role' => 'Soporte'
        ]);

        $response = $this->actingAs($soporte)->get('/users');
        $response->assertStatus(403);
    }

    public function test_superadmin_can_create_user(): void
    {
        $admin = User::create([
            'name' => 'Admin', 'email' => 'admin@test.com', 'password' => 'secret', 'role' => 'SuperAdmin'
        ]);

        $response = $this->actingAs($admin)->post('/users', [
            'name' => 'New User',
            'email' => 'newuser@test.com',
            'password' => 'secret123',
            'role' => 'Ventas'
        ]);

        $response->assertRedirect('/users');
        $this->assertDatabaseHas('users', ['email' => 'newuser@test.com']);
    }

    public function test_soporte_cannot_create_user(): void
    {
        $soporte = User::create([
            'name' => 'Soporte', 'email' => 'soporte@test.com', 'password' => 'secret', 'role' => 'Soporte'
        ]);

        $response = $this->actingAs($soporte)->post('/users', [
            'name' => 'New User',
            'email' => 'newuser2@test.com',
            'password' => 'secret123',
            'role' => 'Ventas'
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('users', ['email' => 'newuser2@test.com']);
    }
}
