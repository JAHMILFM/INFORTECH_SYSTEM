<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Optimizaciones en service_records
        Schema::table('service_records', function (Blueprint $table) {
            // 1. Eliminar índice compuesto ineficiente (si el gestor de BD lo permite)
            // SQL Server requiere el nombre exacto del índice que Laravel generó por defecto.
            // Generalmente es service_records_company_id_type_index
            $table->dropIndex('service_records_company_id_type_index');
            
            // 2. Crear nuevo índice compuesto optimizado
            $table->index(['type', 'company_id'], 'idx_sr_type_company');

            // 3. Columna Computada Virtual para status
            // Usamos virtualAs para que no ocupe espacio, se evalúa con el índice.
            // Nota: SQL Server requiere JSON_VALUE para extraer texto.
            $table->string('status_computed', 50)
                  ->nullable()
                  ->virtualAs("JSON_VALUE(data, '$.status')")
                  ->after('type');

            // 4. Índice de alto rendimiento para el Dashboard
            $table->index(['type', 'status_computed'], 'idx_sr_type_status');
        });

        // Optimizaciones en companies
        Schema::table('companies', function (Blueprint $table) {
            $table->index('domain', 'idx_companies_domain');
            $table->index('created_at', 'idx_companies_created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_records', function (Blueprint $table) {
            $table->dropIndex('idx_sr_type_status');
            $table->dropColumn('status_computed');
            $table->dropIndex('idx_sr_type_company');
            
            // Restaurar el índice original
            $table->index(['company_id', 'type'], 'service_records_company_id_type_index');
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->dropIndex('idx_companies_domain');
            $table->dropIndex('idx_companies_created_at');
        });
    }
};
