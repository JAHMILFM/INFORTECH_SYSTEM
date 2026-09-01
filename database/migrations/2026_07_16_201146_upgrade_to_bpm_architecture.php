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
        // 1. Update Companies Table
        Schema::table('companies', function (Blueprint $table) {
            $table->string('status')->default('active')->after('name');
            $table->string('onboarding_stage')->nullable()->after('status');
            $table->foreignId('account_manager_id')->nullable()->constrained('users')->nullOnDelete()->after('onboarding_stage');
            $table->softDeletes();
        });

        // 2. Update Service Records Table
        Schema::table('service_records', function (Blueprint $table) {
            $table->string('status')->nullable()->after('type');
            $table->date('expiration_date')->nullable()->after('status');
            $table->softDeletes();
        });

        // 3. Update Users & Audit Logs (Soft Deletes)
        Schema::table('users', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->softDeletes();
        });

        // 4. Create Tasks Table for Workflow Engine
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status')->default('pending'); // pending, in_progress, completed
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->date('due_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('service_records', function (Blueprint $table) {
            $table->dropColumn(['status', 'expiration_date']);
            $table->dropSoftDeletes();
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->dropForeign(['account_manager_id']);
            $table->dropColumn(['status', 'onboarding_stage', 'account_manager_id']);
            $table->dropSoftDeletes();
        });
    }
};
