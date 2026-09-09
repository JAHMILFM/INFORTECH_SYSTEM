<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (!Schema::hasColumn('companies', 'branch')) {
                $table->string('branch', 150)->nullable()->after('domain'); // Sede / Local
            }
            if (!Schema::hasColumn('companies', 'area')) {
                $table->string('area', 100)->nullable()->after('branch'); // Área / Departamento
            }
            if (!Schema::hasColumn('companies', 'contact_name')) {
                $table->string('contact_name', 150)->nullable()->after('area'); // Persona de contacto
            }
            if (!Schema::hasColumn('companies', 'contact_phone')) {
                $table->string('contact_phone', 50)->nullable()->after('contact_email'); // Teléfono de contacto
            }
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['branch', 'area', 'contact_name', 'contact_phone']);
        });
    }
};
