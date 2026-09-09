<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('reports')->cascadeOnDelete();
            $table->enum('role', ['delivery', 'reception'])->default('delivery');
            $table->string('signer_name', 150);
            $table->string('signer_role', 100)->nullable(); // Cargo (ej. Técnico Especialista, Jefe de TI)
            $table->longText('signature_data')->nullable(); // Canvas Base64 data URL
            $table->dateTime('signed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_signatures');
    }
};
