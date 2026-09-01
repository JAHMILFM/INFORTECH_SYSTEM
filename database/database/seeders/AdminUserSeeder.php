<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear una empresa por defecto si no existe
        $company = Company::firstOrCreate(
            ['tax_id' => '0000000000'],
            ['name' => 'Infortech Matriz', 'domain' => 'infortech.com']
        );

        User::create([
            'name' => 'Administrador Infortech',
            'email' => 'admin@infortech.com',
            'password' => Hash::make('t$4818043'), // Clave original del usuario
            'role' => 'SuperAdmin'
        ]);
    }
}
