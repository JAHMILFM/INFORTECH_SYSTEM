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
            $table->dropIndex('audit_logs_model_index');
            $table->dropColumn(['model_type', 'model_id']);
            $table->foreignId('company_id')->nullable()->after('action')->constrained('companies');
            $table->foreignId('service_record_id')->nullable()->after('company_id')->constrained('service_records');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropForeign(['service_record_id']);
            $table->dropColumn(['company_id', 'service_record_id']);
            
            $table->string('model_type')->after('action');
            $table->unsignedBigInteger('model_id')->after('model_type');
            $table->index(['model_type', 'model_id'], 'audit_logs_model_index');
        });
    }
};
