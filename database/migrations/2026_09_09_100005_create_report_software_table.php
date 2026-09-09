<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_software', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('reports')->cascadeOnDelete();
            $table->foreignId('software_id')->nullable()->constrained('software_catalog')->nullOnDelete();
            $table->string('software_name', 150)->nullable();
            $table->string('detail', 255)->nullable(); // Detalles específicos (ej. AutoCAD 2024, Acrobat Pro)
            $table->boolean('is_installed')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_software');
    }
};
