<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Modules\Company\Services\CompanyService;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CompanyServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CompanyService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CompanyService();
    }

    public function test_create_company()
    {
        $data = [
            'name' => 'Acme Corp',
            'tax_id' => '10203040506',
            'domain' => 'acme.com',
            'contact_email' => 'admin@acme.com'
        ];

        $company = $this->service->createCompany($data);

        $this->assertInstanceOf(Company::class, $company);
        $this->assertEquals('Acme Corp', $company->name);
        $this->assertEquals('10203040506', $company->tax_id);
    }

    public function test_update_company_cleans_empty_fields()
    {
        $company = Company::create([
            'name' => 'Acme Corp',
            'tax_id' => '10203040506',
            'domain' => 'acme.com',
            'contact_email' => 'admin@acme.com'
        ]);

        $data = [
            'name' => 'Acme Corp Updated',
            'tax_id' => '10203040506',
            'domain' => '', // Should become null
            'contact_email' => '' // Should become null
        ];

        $this->service->updateCompany($company, $data);

        $company->refresh();
        $this->assertEquals('Acme Corp Updated', $company->name);
        $this->assertEquals('', $company->domain);
        $this->assertEquals('', $company->contact_email);
    }
}
