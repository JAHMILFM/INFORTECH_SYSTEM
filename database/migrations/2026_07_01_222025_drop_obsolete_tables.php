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
        // Drop tables in reverse order of creation (due to foreign keys)
        Schema::dropIfExists('credential_document');
        Schema::dropIfExists('access_credentials');
        Schema::dropIfExists('documents');
        Schema::dropIfExists('folders');
        Schema::dropIfExists('categories');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No down migration since these are obsolete tables that were replaced by service_records.
    }
};
