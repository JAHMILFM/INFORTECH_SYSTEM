<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ReportType;
use App\Models\SoftwareCatalog;

class ReportModuleSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tipo de reporte base: Formateo
        ReportType::firstOrCreate(
            ['slug' => 'formateo'],
            [
                'name'        => 'Formateo y Mantenimiento de Equipos',
                'format_code' => 'FOR-TI-001',
                'version'     => '02',
                'description' => 'Documento de constancia de servicio técnico, configuración inicial y entrega de equipos de cómputo.',
                'is_active'   => true,
            ]
        );

        // 2. Catálogo base de Software
        $softwareList = [
            ['name' => 'Microsoft Office',      'category' => 'Ofimática',       'requires_detail' => false, 'sort_order' => 1],
            ['name' => 'Microsoft Teams',       'category' => 'Colaboración',    'requires_detail' => false, 'sort_order' => 2],
            ['name' => 'Microsoft OneDrive',    'category' => 'Almacenamiento',  'requires_detail' => false, 'sort_order' => 3],
            ['name' => 'AnyDesk',               'category' => 'Soporte Remoto',  'requires_detail' => false, 'sort_order' => 4],
            ['name' => 'Autodesk (+ extensiones)','category' => 'CAD / Diseño',  'requires_detail' => true,  'sort_order' => 5],
            ['name' => 'Adobe (Productos)',     'category' => 'Diseño Gráfico',  'requires_detail' => true,  'sort_order' => 6],
            ['name' => 'Google Chrome',         'category' => 'Navegación',      'requires_detail' => false, 'sort_order' => 7],
            ['name' => 'WinRAR / 7-Zip',        'category' => 'Utilidades',      'requires_detail' => false, 'sort_order' => 8],
            ['name' => 'Zoom Meetings',         'category' => 'Colaboración',    'requires_detail' => false, 'sort_order' => 9],
        ];

        foreach ($softwareList as $item) {
            SoftwareCatalog::firstOrCreate(
                ['name' => $item['name']],
                $item
            );
        }
    }
}
