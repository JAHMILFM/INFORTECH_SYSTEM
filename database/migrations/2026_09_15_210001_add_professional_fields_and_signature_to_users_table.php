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
        Schema::table('users', function (Blueprint $table) {
            $table->string('job_title', 150)->nullable()->after('email');
            $table->string('phone', 50)->nullable()->after('job_title');
            $table->string('document_id', 20)->nullable()->after('phone');
            $table->longText('signature_data')->nullable()->after('role');
            $table->dateTime('signature_updated_at')->nullable()->after('signature_data');
            $table->string('avatar', 255)->nullable()->after('signature_updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'job_title',
                'phone',
                'document_id',
                'signature_data',
                'signature_updated_at',
                'avatar',
            ]);
        });
    }
};
