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
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            // Mantenemos la columna company_id para propósitos de bitácora
            // pero quitamos el constraint para evitar el error de Foreign Key Lockout.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->foreign('company_id')
                  ->references('id')
                  ->on('companies');
        });
    }
};
