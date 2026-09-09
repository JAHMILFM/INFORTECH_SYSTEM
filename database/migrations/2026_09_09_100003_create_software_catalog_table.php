<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('software_catalog', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('category', 100)->nullable(); // Ofimática, Diseño / CAD, Soporte, etc.
            $table->boolean('requires_detail')->default(false); // Para Autodesk / Adobe que permiten especificar producto
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('software_catalog');
    }
};
