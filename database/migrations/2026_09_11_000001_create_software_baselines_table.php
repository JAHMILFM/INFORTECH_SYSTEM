<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('software_baselines')) {
            Schema::create('software_baselines', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100);
                $table->text('description')->nullable();
                $table->string('icon', 50)->default('bi-briefcase-fill');
                $table->json('software_ids')->nullable();
                $table->boolean('is_default')->default(false);
                $table->timestamps();
            });
        }

        // Insert initial baseline templates if software catalog has items
        if (Schema::hasTable('software_catalog')) {
            $allSoftware = DB::table('software_catalog')->where('is_active', 1)->get();

            if ($allSoftware->isNotEmpty() && DB::table('software_baselines')->count() === 0) {
                $getId = function($name) use ($allSoftware) {
                    $item = $allSoftware->first(function($s) use ($name) {
                        return stripos($s->name, $name) !== false;
                    });
                    return $item ? $item->id : null;
                };

                $adminSoft = array_values(array_filter([
                    $getId('Office'),
                    $getId('Teams'),
                    $getId('AnyDesk'),
                    $getId('Chrome'),
                    $getId('Acrobat'),
                    $getId('WinRAR')
                ]));

                $designSoft = array_values(array_filter([
                    $getId('Photoshop'),
                    $getId('Illustrator'),
                    $getId('AutoCAD'),
                    $getId('Office'),
                    $getId('Chrome')
                ]));

                $devSoft = array_values(array_filter([
                    $getId('Visual Studio Code'),
                    $getId('Git'),
                    $getId('Docker'),
                    $getId('Postman'),
                    $getId('DBeaver'),
                    $getId('Chrome')
                ]));

                $basicSoft = array_values(array_filter([
                    $getId('Chrome'),
                    $getId('WinRAR'),
                    $getId('Acrobat'),
                    $getId('NOD32') ?: $getId('Antivirus')
                ]));

                DB::table('software_baselines')->insert([
                    [
                        'name' => 'Administrativo / Ofimática',
                        'description' => 'Perfil estándar para estaciones de oficina y finanzas',
                        'icon' => 'bi-briefcase-fill',
                        'software_ids' => json_encode($adminSoft),
                        'is_default' => 1,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ],
                    [
                        'name' => 'Diseño & Ingeniería',
                        'description' => 'Estaciones multimedia, edición gráfica y modelado CAD',
                        'icon' => 'bi-palette-fill',
                        'software_ids' => json_encode($designSoft),
                        'is_default' => 0,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ],
                    [
                        'name' => 'Desarrollo & TI',
                        'description' => 'Equipos para ingenieros de sistemas y programadores',
                        'icon' => 'bi-code-slash',
                        'software_ids' => json_encode($devSoft),
                        'is_default' => 0,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ],
                    [
                        'name' => 'Básico / Formateo Limpio',
                        'description' => 'Mantenimiento inicial y utilidades mínimas recomendadas',
                        'icon' => 'bi-shield-check',
                        'software_ids' => json_encode($basicSoft),
                        'is_default' => 0,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('software_baselines');
    }
};