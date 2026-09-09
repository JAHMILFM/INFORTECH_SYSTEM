<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_type_id')->constrained('report_types')->onDelete('no action');
            $table->string('code', 50)->unique()->index(); // FOR-TI-001-2026-0001
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('equipment_id')->constrained('equipment')->onDelete('no action');
            $table->foreignId('technician_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('technician_name', 150)->nullable();
            $table->date('service_date');
            $table->enum('status', ['draft', 'confirmed', 'cancelled'])->default('draft')->index();
            $table->json('data')->nullable(); // Esquema dinámico de Formateo (Secciones B, C, D.2, etc.)
            $table->text('notes')->nullable(); // Observaciones finales
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
