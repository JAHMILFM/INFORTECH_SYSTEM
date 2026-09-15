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
        Schema::table('tasks', function (Blueprint $table) {
            $table->index('company_id', 'idx_tasks_company_id');
            $table->index('assigned_to', 'idx_tasks_assigned_to');
            $table->index('status', 'idx_tasks_status');
            $table->index('due_date', 'idx_tasks_due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex('idx_tasks_company_id');
            $table->dropIndex('idx_tasks_assigned_to');
            $table->dropIndex('idx_tasks_status');
            $table->dropIndex('idx_tasks_due_date');
        });
    }
};
