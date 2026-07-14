<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Company;

class CompanyFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_can_access_companies_index(): void
    {
        $admin = User::create([
            'name' => 'Admin', 'email' => 'admin@test.com', 'password' => 'secret', 'role' => 'SuperAdmin'
        ]);

        $response = $this->actingAs($admin)->get('/companies');
        $response->assertStatus(200);
        $response->assertViewIs('companies.index');
    }

    public function test_unauthenticated_user_cannot_access_companies(): void
    {
        $response = $this->get('/companies');
        $response->assertRedirect('/login');
    }

    public function test_superadmin_can_create_company(): void
    {
        $admin = User::create([
            'name' => 'Admin', 'email' => 'admin@test.com', 'password' => 'secret', 'role' => 'SuperAdmin'
        ]);

        $response = $this->actingAs($admin)->post('/companies', [
            'name' => 'Test Company',
            'tax_id' => '1122334455',
            'domain' => 'testcompany.com'
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('companies', ['tax_id' => '1122334455']);
    }

    public function test_soporte_can_create_company(): void
    {
        $soporte = User::create([
            'name' => 'Soporte', 'email' => 'soporte@test.com', 'password' => 'secret', 'role' => 'Soporte'
        ]);

        $response = $this->actingAs($soporte)->post('/companies', [
            'name' => 'Support Company',
            'tax_id' => '9988776655',
            'domain' => 'support.com'
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('companies', ['tax_id' => '9988776655']);
    }

    public function test_ventas_cannot_create_company(): void
    {
        $ventas = User::create([
            'name' => 'Ventas', 'email' => 'ventas@test.com', 'password' => 'secret', 'role' => 'Ventas'
        ]);

        $response = $this->actingAs($ventas)->post('/companies', [
            'name' => 'Sales Company',
            'tax_id' => '5544332211',
            'domain' => 'sales.com'
        ]);

        // Ventas no tiene permisos de escritura (403)
        $response->assertStatus(403);
        $this->assertDatabaseMissing('companies', ['tax_id' => '5544332211']);
    }

    public function test_only_superadmin_can_delete_company(): void
    {
        $admin = User::create([
            'name' => 'Admin', 'email' => 'admin@test.com', 'password' => 'secret', 'role' => 'SuperAdmin'
        ]);
        $soporte = User::create([
            'name' => 'Soporte', 'email' => 'soporte@test.com', 'password' => 'secret', 'role' => 'Soporte'
        ]);
        
        $company = Company::create([
            'name' => 'To Delete', 'tax_id' => '000000000'
        ]);

        // Soporte shouldn't be able to delete
        $response = $this->actingAs($soporte)->delete('/companies/' . $company->id);
        $response->assertStatus(403);

        // Admin should be able to delete
        $response = $this->actingAs($admin)->delete('/companies/' . $company->id);
        $response->assertRedirect();
        $this->assertDatabaseMissing('companies', ['id' => $company->id]);
    }
}
