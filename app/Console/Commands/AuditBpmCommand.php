<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Task;
use App\Models\Company;
use App\Models\User;
use App\Models\ServiceRecord;

class AuditBpmCommand extends Command
{
    protected $signature = 'audit:bpm';
    protected $description = 'Audita la arquitectura de base de datos del módulo BPM y seguridad';

    public function handle()
    {
        $this->info("=================================================");
        $this->info(" AUDITORÍA INTEGRAL: BASE DE DATOS Y ARQUITECTURA BPM");
        $this->info("=================================================");

        // 1. Tablas
        $this->line("\n[1] ESTRUCTURA DE TABLAS");
        $tables = ['tasks', 'companies', 'service_records', 'users', 'audit_logs', 'reports', 'report_signatures'];
        foreach ($tables as $tbl) {
            $exists = Schema::hasTable($tbl);
            $count = $exists ? DB::table($tbl)->count() : 0;
            $this->line("  - {$tbl}: " . ($exists ? "ACTIVA ({$count} registros)" : "NO EXISTE"));
        }

        // 2. Columnas en Companies
        $this->line("\n[2] COLUMNAS BPM EN 'companies'");
        foreach (['status', 'onboarding_stage', 'account_manager_id', 'deleted_at'] as $col) {
            $this->line("  - companies.{$col}: " . (Schema::hasColumn('companies', $col) ? "CORRECTO" : "FALTA"));
        }

        // 3. Columnas en Tasks
        $this->line("\n[3] COLUMNAS EN 'tasks'");
        foreach (['id', 'title', 'description', 'status', 'company_id', 'assigned_to', 'due_date', 'deleted_at'] as $col) {
            $this->line("  - tasks.{$col}: " . (Schema::hasColumn('tasks', $col) ? "CORRECTO" : "FALTA"));
        }

        // 4. Integridad Referencial y Huérfanos
        $this->line("\n[4] INTEGRIDAD REFERENCIAL Y CONTROL DE HUÉRFANOS");
        $orphanTaskComp = DB::table('tasks')
            ->whereNotNull('company_id')
            ->whereNotIn('company_id', DB::table('companies')->select('id'))
            ->count();
        $this->line("  - Tareas con company_id huérfano: " . ($orphanTaskComp === 0 ? "0 (Integridad OK)" : "{$orphanTaskComp} registros huérfanos detectados"));

        $orphanTaskUser = DB::table('tasks')
            ->whereNotNull('assigned_to')
            ->whereNotIn('assigned_to', DB::table('users')->select('id'))
            ->count();
        $this->line("  - Tareas con assigned_to huérfano: " . ($orphanTaskUser === 0 ? "0 (Integridad OK)" : "{$orphanTaskUser} registros huérfanos detectados"));

        $orphanCompMgr = DB::table('companies')
            ->whereNotNull('account_manager_id')
            ->whereNotIn('account_manager_id', DB::table('users')->select('id'))
            ->count();
        $this->line("  - Empresas con account_manager_id huérfano: " . ($orphanCompMgr === 0 ? "0 (Integridad OK)" : "{$orphanCompMgr} registros huérfanos detectados"));

        // 5. Análisis de Índices de Base de Datos
        $this->line("\n[5] ANÁLISIS DE ÍNDICES EN TABLAS BPM");
        foreach (['tasks', 'companies', 'service_records'] as $tableName) {
            $this->line("  >> Tabla '{$tableName}':");
            $indexes = DB::select("
                SELECT i.name AS index_name, c.name AS column_name, i.is_unique, i.type_desc
                FROM sys.indexes i
                INNER JOIN sys.index_columns ic ON i.object_id = ic.object_id AND i.index_id = ic.index_id
                INNER JOIN sys.columns c ON ic.object_id = c.object_id AND ic.column_id = c.column_id
                WHERE i.object_id = OBJECT_ID(?)
                ORDER BY i.name, ic.key_ordinal
            ", [$tableName]);

            if (empty($indexes)) {
                $this->warn("     No se encontraron índices en '{$tableName}'.");
            } else {
                foreach ($indexes as $idx) {
                    $this->line("     * {$idx->index_name} -> [{$idx->column_name}] (Único: " . ($idx->is_unique ? "SÍ" : "NO") . ", Tipo: {$idx->type_desc})");
                }
            }
        }

        $this->info("\nAuditoría ejecutada satisfactoriamente.");
        return 0;
    }
}
