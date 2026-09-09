<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Company;
use App\Models\Equipment;
use App\Models\Report;
use App\Models\ReportType;
use App\Models\SoftwareCatalog;
use App\Modules\Report\Services\ReportCodeGenerator;
use Illuminate\Support\Facades\Crypt;

class ReportModuleTest extends TestCase
{
    public function test_report_code_generation()
    {
        $reportType = ReportType::where('slug', 'formateo')->first();
        $this->assertNotNull($reportType);

        $generator = new ReportCodeGenerator();
        $code = $generator->generate($reportType);
        $this->assertStringStartsWith('FOR-TI-001-', $code);
    }

    public function test_create_equipment_and_report_with_encrypted_password()
    {
        $company = Company::firstOrCreate(
            ['tax_id' => '20123456789'],
            ['name' => 'Empresa Test SAC', 'domain' => 'empresatest.com']
        );

        $reportType = ReportType::where('slug', 'formateo')->first();

        $equipment = Equipment::create([
            'company_id'    => $company->id,
            'type'          => 'laptop',
            'serial_number' => 'TEST-SN-' . uniqid(),
            'brand'         => 'Lenovo',
            'model'         => 'ThinkPad E14',
            'os'            => 'Windows 11 Pro',
        ]);

        $generator = new ReportCodeGenerator();
        $code = $generator->generate($reportType);

        $rawPassword = 'MiPasswordSeguro123!';
        $report = Report::create([
            'report_type_id'  => $reportType->id,
            'code'            => $code,
            'company_id'      => $company->id,
            'equipment_id'    => $equipment->id,
            'technician_name' => 'Técnico Especialista',
            'service_date'    => now()->format('Y-m-d'),
            'status'          => 'draft',
            'data'            => [
                'user_name'                 => 'Usuario Demo',
                'credentials_configured'    => true,
                'encrypted_access_password' => Crypt::encryptString($rawPassword),
                'backup_done'               => true,
                'antivirus_installed'       => true,
                'antivirus_name'            => 'Windows Defender',
                'license_activated'         => true,
                'drivers_installed'         => true,
            ],
            'notes' => 'Mantenimiento preventivo y formateo completado.',
        ]);

        $this->assertEquals($code, $report->code);
        $this->assertEquals($rawPassword, $report->decrypted_password);
        $this->assertTrue($report->isDraft());

        // Confirmar reporte
        $report->update(['status' => 'confirmed']);
        $this->assertTrue($report->fresh()->isConfirmed());

        // Limpieza del test
        $report->forceDelete();
        $equipment->forceDelete();
    }

    public function test_software_catalog_items_exist()
    {
        $count = SoftwareCatalog::count();
        $this->assertGreaterThanOrEqual(9, $count);

        $office = SoftwareCatalog::where('name', 'Microsoft Office')->first();
        $this->assertNotNull($office);

        $autodesk = SoftwareCatalog::where('name', 'like', '%Autodesk%')->first();
        $this->assertNotNull($autodesk);
        $this->assertTrue($autodesk->requires_detail);
    }
}
