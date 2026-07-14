<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Modules\ServiceRecord\Services\ServiceRecordService;
use App\Models\Company;
use App\Models\ServiceRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;

class ServiceRecordServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ServiceRecordService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ServiceRecordService();
    }

    public function test_apply_poka_yoke_email_strips_domain()
    {
        $company = Company::create(['name' => 'Test', 'domain' => 'testcompany.com', 'tax_id' => '123456789']);
        $inputData = ['address_prefix' => 'john.doe@testcompany.com'];

        $this->service->applyPokaYokeEmail('email', $inputData, $company);

        $this->assertEquals('john.doe@testcompany.com', $inputData['address']);
    }

    public function test_apply_poka_yoke_email_adds_domain()
    {
        $company = Company::create(['name' => 'Test', 'domain' => 'testcompany.com', 'tax_id' => '123456789']);
        $inputData = ['address_prefix' => 'jane.doe'];

        $this->service->applyPokaYokeEmail('email', $inputData, $company);

        $this->assertEquals('jane.doe@testcompany.com', $inputData['address']);
    }

    public function test_create_record_encrypts_password()
    {
        $company = Company::create(['name' => 'Test', 'tax_id' => '123456789']);
        $data = [
            'username' => 'admin',
            'password' => 'secret123',
        ];

        $record = $this->service->createRecord($company, 'account', $data);

        $this->assertArrayHasKey('password', $record->data);
        
        $rawData = json_decode($record->getAttributes()['data'], true);
        $this->assertNotEquals('secret123', $rawData['password']);
        
        // Ensure decrypting yields original
        $this->assertEquals('secret123', $record->data['password']); // mutator decrypts it automatically
    }

    public function test_update_record_preserves_password_if_null_provided()
    {
        $company = Company::create(['name' => 'Test', 'tax_id' => '123456789']);
        $record = $this->service->createRecord($company, 'account', ['username' => 'admin', 'password' => 'secret']);
        
        $this->service->updateRecord($record, ['username' => 'newadmin', 'password' => null]);
        
        $record->refresh();
        $this->assertEquals('newadmin', $record->data['username']);
        $this->assertEquals('secret', $record->data['password']); // Password shouldn't change
    }

    public function test_update_record_changes_password_if_provided()
    {
        $company = Company::create(['name' => 'Test', 'tax_id' => '123456789']);
        $record = $this->service->createRecord($company, 'account', ['username' => 'admin', 'password' => 'secret']);
        
        $this->service->updateRecord($record, ['username' => 'admin', 'password' => 'newsecret']);
        
        $record->refresh();
        $this->assertEquals('newsecret', $record->data['password']);
    }
}
