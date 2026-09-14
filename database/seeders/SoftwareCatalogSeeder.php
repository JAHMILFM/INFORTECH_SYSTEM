<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SoftwareCatalog;
use App\Models\SoftwareBaseline;

class SoftwareCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $catalogStructure = [
            [
                'category' => 'Diseño CAD / BIM / Ingeniería',
                'description' => 'Programas técnicos para arquitectura, ingeniería, planos, modelado BIM, revisión de proyectos y extensiones de AutoCAD.',
                'programs' => [
                    ['name' => 'AutoCAD', 'requires_detail' => true, 'default_version' => '2024 (64-bit)'],
                    ['name' => 'Revit', 'requires_detail' => true, 'default_version' => '2024'],
                    ['name' => 'Navisworks', 'requires_detail' => true, 'default_version' => 'Manage 2024'],
                    ['name' => 'CADWorx', 'requires_detail' => true, 'default_version' => 'Plant Professional'],
                ]
            ],
            [
                'category' => 'Modelado 3D / Diseño visual',
                'description' => 'Modelado 3D, diseño arquitectónico, visualización y renderizado.',
                'programs' => [
                    ['name' => 'SketchUp', 'requires_detail' => true, 'default_version' => 'Pro 2024'],
                    ['name' => 'V-Ray', 'requires_detail' => true, 'default_version' => 'v6.0 para SketchUp'],
                    ['name' => 'Lumion', 'requires_detail' => true, 'default_version' => 'v12 / 2023'],
                ]
            ],
            [
                'category' => 'Ofimática / Microsoft Office',
                'description' => 'Documentos, hojas de cálculo, presentaciones, correo y paquetes de oficina.',
                'programs' => [
                    ['name' => 'Microsoft Office (Paquete Completo)', 'requires_detail' => true, 'default_version' => 'Office 2021 LTSC Pro Plus'],
                    ['name' => 'Microsoft Word', 'requires_detail' => true, 'default_version' => 'Office 365 / 2021'],
                    ['name' => 'Microsoft Excel', 'requires_detail' => true, 'default_version' => 'Office 365 / 2021'],
                    ['name' => 'Microsoft PowerPoint', 'requires_detail' => true, 'default_version' => 'Office 365 / 2021'],
                    ['name' => 'Microsoft Outlook', 'requires_detail' => true, 'default_version' => 'Configurado con Zimbra / IMAP'],
                ]
            ],
            [
                'category' => 'Diagramación y gestión de proyectos',
                'description' => 'Diagramas, flujogramas, cronogramas y gestión de proyectos.',
                'programs' => [
                    ['name' => 'Microsoft Visio', 'requires_detail' => true, 'default_version' => 'Professional 2021'],
                    ['name' => 'Microsoft Project', 'requires_detail' => true, 'default_version' => 'Professional 2021'],
                ]
            ],
            [
                'category' => 'Comunicación y colaboración',
                'description' => 'Reuniones, chat corporativo, almacenamiento en nube y trabajo colaborativo.',
                'programs' => [
                    ['name' => 'Microsoft Teams', 'requires_detail' => false, 'default_version' => 'App Corporativa'],
                    ['name' => 'Microsoft OneDrive', 'requires_detail' => false, 'default_version' => 'Sincronización Activa'],
                    ['name' => 'Zoom Meetings', 'requires_detail' => false, 'default_version' => 'Cliente de escritorio'],
                    ['name' => 'Nextcloud Desktop Client', 'requires_detail' => false, 'default_version' => 'Sincronización Infortech'],
                ]
            ],
            [
                'category' => 'PDF / Documentos digitales',
                'description' => 'Lectura, edición, firma digital o gestión de archivos PDF.',
                'programs' => [
                    ['name' => 'Adobe Acrobat / Acrobat Reader', 'requires_detail' => true, 'default_version' => 'Acrobat Reader 64-bit / Pro DC'],
                    ['name' => 'Foxit PDF Reader', 'requires_detail' => false, 'default_version' => 'v13'],
                    ['name' => 'Nitro Pro', 'requires_detail' => true, 'default_version' => 'v14'],
                ]
            ],
            [
                'category' => 'Business Intelligence / Reportes',
                'description' => 'Dashboards, reportes, análisis de datos e indicadores.',
                'programs' => [
                    ['name' => 'Power BI', 'requires_detail' => true, 'default_version' => 'Desktop 64-bit'],
                ]
            ],
            [
                'category' => 'Antivirus / Seguridad',
                'description' => 'Protección contra virus, malware y amenazas.',
                'programs' => [
                    ['name' => 'ESET NOD32', 'requires_detail' => true, 'default_version' => 'Endpoint Security v11'],
                    ['name' => 'Kaspersky', 'requires_detail' => true, 'default_version' => 'Endpoint Security'],
                    ['name' => 'Bitdefender', 'requires_detail' => true, 'default_version' => 'GravityZone Business'],
                    ['name' => 'Windows Defender', 'requires_detail' => false, 'default_version' => 'Activo y actualizado'],
                ]
            ],
            [
                'category' => 'Acceso remoto / Soporte remoto',
                'description' => 'Soporte técnico remoto, conexión a equipos de clientes o usuarios.',
                'programs' => [
                    ['name' => 'RustDesk', 'requires_detail' => false, 'default_version' => 'Servidor Infortech'],
                    ['name' => 'AnyDesk', 'requires_detail' => false, 'default_version' => 'v8.x'],
                    ['name' => 'TeamViewer', 'requires_detail' => false, 'default_version' => 'Host / Completo'],
                ]
            ],
            [
                'category' => 'Navegadores web',
                'description' => 'Acceso a internet, sistemas web, plataformas online.',
                'programs' => [
                    ['name' => 'Google Chrome', 'requires_detail' => false, 'default_version' => '64-bit'],
                    ['name' => 'Mozilla Firefox', 'requires_detail' => false, 'default_version' => '64-bit'],
                    ['name' => 'Microsoft Edge', 'requires_detail' => false, 'default_version' => 'Predeterminado'],
                ]
            ],
            [
                'category' => 'Compresión de archivos',
                'description' => 'Comprimir y descomprimir archivos .rar, .zip, etc.',
                'programs' => [
                    ['name' => 'WinRAR', 'requires_detail' => true, 'default_version' => '7.01 (64-bit)'],
                    ['name' => '7-Zip', 'requires_detail' => false, 'default_version' => '24.05 (64-bit)'],
                ]
            ],
            [
                'category' => 'Desarrollo / Programación',
                'description' => 'Programación, automatización, scripts, desarrollo de sistemas.',
                'programs' => [
                    ['name' => 'Python', 'requires_detail' => true, 'default_version' => '3.12 (64-bit)'],
                    ['name' => 'Visual Studio Code', 'requires_detail' => false, 'default_version' => 'Última versión'],
                    ['name' => 'Visual Studio', 'requires_detail' => true, 'default_version' => '2022 Community / Pro'],
                ]
            ],
            [
                'category' => 'Base de datos / Consultas',
                'description' => 'Administración y consulta de bases de datos.',
                'programs' => [
                    ['name' => 'SQL Server Management Studio', 'requires_detail' => true, 'default_version' => 'SSMS v19 / v20'],
                    ['name' => 'MySQL Workbench', 'requires_detail' => true, 'default_version' => '8.0 CE'],
                    ['name' => 'DBeaver', 'requires_detail' => true, 'default_version' => 'Community 24.x'],
                ]
            ],
            [
                'category' => 'Herramientas para desarrolladores',
                'description' => 'Complementos para desarrollo, APIs, control de versiones y entornos de programación.',
                'programs' => [
                    ['name' => 'Git', 'requires_detail' => true, 'default_version' => '2.45 (64-bit)'],
                    ['name' => 'Node.js', 'requires_detail' => true, 'default_version' => 'v20 LTS / v22'],
                    ['name' => 'Java JDK', 'requires_detail' => true, 'default_version' => 'OpenJDK 17 / 21 LTS'],
                    ['name' => '.NET SDK', 'requires_detail' => true, 'default_version' => '.NET 8.0 SDK'],
                    ['name' => 'Postman', 'requires_detail' => false, 'default_version' => 'Desktop Agent'],
                    ['name' => 'Docker Desktop', 'requires_detail' => true, 'default_version' => 'WSL2 Backend'],
                ]
            ],
            [
                'category' => 'Utilitarios del sistema',
                'description' => 'Herramientas de mantenimiento, diagnóstico o soporte.',
                'programs' => [
                    ['name' => '7-Zip', 'requires_detail' => false, 'default_version' => '64-bit'],
                    ['name' => 'CCleaner', 'requires_detail' => false, 'default_version' => 'v6.x'],
                    ['name' => 'CrystalDiskInfo', 'requires_detail' => false, 'default_version' => 'Salud de Disco / SSD'],
                    ['name' => 'HWiNFO / CPU-Z', 'requires_detail' => false, 'default_version' => 'Diagnóstico Hardware'],
                ]
            ],
            [
                'category' => 'Drivers / Componentes necesarios',
                'description' => 'Componentes que no siempre son programas principales, pero son necesarios para que otros sistemas funcionen.',
                'programs' => [
                    ['name' => 'Drivers NVIDIA', 'requires_detail' => true, 'default_version' => 'GeForce Game Ready / Studio'],
                    ['name' => 'Drivers Impresora', 'requires_detail' => true, 'default_version' => 'HP / Epson / Canon / Kyocera'],
                    ['name' => 'Microsoft Visual C++ Redistributable', 'requires_detail' => true, 'default_version' => '2015-2022 AIO (x86/x64)'],
                    ['name' => '.NET Runtime', 'requires_detail' => true, 'default_version' => '.NET Desktop Runtime 8.0 / 6.0'],
                ]
            ],
            [
                'category' => 'Licenciamiento / Activación',
                'description' => 'Servicios o componentes relacionados con licencias. Útil para reportes técnicos.',
                'programs' => [
                    ['name' => 'Autodesk Licensing Service', 'requires_detail' => true, 'default_version' => 'Single-User / Flex'],
                    ['name' => 'Office Licensing', 'requires_detail' => true, 'default_version' => 'Microsoft 365 / OEM / KMS'],
                    ['name' => 'Adobe Licensing', 'requires_detail' => true, 'default_version' => 'Enterprise / Named User'],
                    ['name' => 'Windows Activation', 'requires_detail' => true, 'default_version' => 'Digital License / OEM Pro'],
                ]
            ],
            [
                'category' => 'Otros / Sin clasificar',
                'description' => 'Esta categoría debería usarse lo menos posible. Luego cada software se debe mover a una categoría correcta.',
                'programs' => [
                    ['name' => 'Software Especializado / Adicional', 'requires_detail' => true, 'default_version' => 'Especificar nombre y versión'],
                ]
            ]
        ];

        $order = 1;
        foreach ($catalogStructure as $catData) {
            $catName = $catData['category'];
            $catDesc = $catData['description'];

            foreach ($catData['programs'] as $prog) {
                SoftwareCatalog::updateOrCreate(
                    [
                        'name'     => $prog['name'],
                        'category' => $catName,
                    ],
                    [
                        'description'     => $catDesc,
                        'requires_detail' => $prog['requires_detail'],
                        'default_version' => $prog['default_version'] ?? null,
                        'is_active'       => true,
                        'sort_order'      => $order++,
                    ]
                );
            }
        }

        // Actualizar o crear perfiles recomendados (Software Baselines)
        $all = SoftwareCatalog::where('is_active', 1)->get();
        $getId = function($name) use ($all) {
            $found = $all->first(function($s) use ($name) {
                return stripos($s->name, $name) !== false;
            });
            return $found ? $found->id : null;
        };

        $baselines = [
            [
                'name' => 'Administrativo / Ofimática',
                'description' => 'Perfil estándar para estaciones de oficina, facturación y finanzas.',
                'icon' => 'bi-briefcase-fill',
                'is_default' => 1,
                'software_names' => [
                    'Microsoft Office (Paquete Completo)',
                    'Microsoft Teams',
                    'AnyDesk',
                    'Google Chrome',
                    'Adobe Acrobat',
                    'WinRAR',
                    'ESET NOD32'
                ]
            ],
            [
                'name' => 'Diseño CAD & Ingeniería',
                'description' => 'Estaciones para arquitectos, ingenieros civiles, modeladores BIM y diseñadores.',
                'icon' => 'bi-palette-fill',
                'is_default' => 0,
                'software_names' => [
                    'AutoCAD',
                    'Revit',
                    'Navisworks',
                    'SketchUp',
                    'Microsoft Office (Paquete Completo)',
                    'Adobe Acrobat',
                    'Google Chrome',
                    'WinRAR',
                    'Drivers NVIDIA',
                    'Autodesk Licensing Service'
                ]
            ],
            [
                'name' => 'Desarrollo & TI',
                'description' => 'Equipos para programadores, analistas de sistemas y administradores de BD.',
                'icon' => 'bi-code-slash',
                'is_default' => 0,
                'software_names' => [
                    'Visual Studio Code',
                    'Python',
                    'Git',
                    'Node.js',
                    'SQL Server Management Studio',
                    'DBeaver',
                    'Postman',
                    'Google Chrome',
                    'WinRAR'
                ]
            ],
            [
                'name' => 'Básico / Mantenimiento Limpio',
                'description' => 'Perfil mínimo para entrega tras formateo limpio o mantenimiento preventivo.',
                'icon' => 'bi-shield-check',
                'is_default' => 0,
                'software_names' => [
                    'Google Chrome',
                    'Adobe Acrobat',
                    'WinRAR',
                    'AnyDesk',
                    'Microsoft Visual C++',
                    '.NET Runtime',
                    'Windows Defender'
                ]
            ],
        ];

        foreach ($baselines as $b) {
            $ids = [];
            foreach ($b['software_names'] as $sName) {
                $id = $getId($sName);
                if ($id) {
                    $ids[] = $id;
                }
            }

            SoftwareBaseline::updateOrCreate(
                ['name' => $b['name']],
                [
                    'description'  => $b['description'],
                    'icon'         => $b['icon'],
                    'software_ids' => array_values(array_unique($ids)),
                    'is_default'   => (bool)$b['is_default'],
                ]
            );
        }
    }
}