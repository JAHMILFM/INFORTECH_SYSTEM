<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('software_catalog', function (Blueprint $table) {
            if (!Schema::hasColumn('software_catalog', 'description')) {
                $table->string('description', 255)->nullable();
            }
            if (!Schema::hasColumn('software_catalog', 'default_version')) {
                $table->string('default_version', 100)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('software_catalog', function (Blueprint $table) {
            if (Schema::hasColumn('software_catalog', 'description')) {
                $table->dropColumn('description');
            }
            if (Schema::hasColumn('software_catalog', 'default_version')) {
                $table->dropColumn('default_version');
            }
        });
    }
};
