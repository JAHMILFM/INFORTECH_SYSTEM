<?php

namespace App\Listeners;

use App\Events\CompanyCreated;
use App\Models\Task;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Carbon;

class CreateOnboardingTasks
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(CompanyCreated $event): void
    {
        $company = $event->company;

        // Tarea 1: Asignar Ejecutivo de Cuenta
        Task::create([
            'title' => 'Asignar Ejecutivo de Cuenta',
            'description' => 'Asignar un Ejecutivo de Cuenta principal para ' . $company->name,
            'status' => 'pending',
            'company_id' => $company->id,
            'due_date' => Carbon::now()->addDays(1),
        ]);

        // Tarea 2: Provisionar Entorno Inicial (Zimbra, Nextcloud)
        Task::create([
            'title' => 'Provisionamiento Técnico Inicial',
            'description' => 'Configurar correos en Zimbra y espacio en Nextcloud para ' . $company->name,
            'status' => 'pending',
            'company_id' => $company->id,
            'due_date' => Carbon::now()->addDays(2),
        ]);
    }
}
