<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuario admin de prueba si no existe
        User::firstOrCreate(
            ['email' => 'admin@gym.com'],
            [
                'name' => 'Administrador',
                'email' => 'admin@gym.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Crear usuario secretario de prueba si no existe
        User::firstOrCreate(
            ['email' => 'secretario@gym.com'],
            [
                'name' => 'Secretario',
                'email' => 'secretario@gym.com',
                'password' => Hash::make('password'),
                'role' => 'secretario',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Usuarios de prueba creados:');
        $this->command->info('Admin: admin@gym.com / password');
        $this->command->info('Secretario: secretario@gym.com / password');
    }
}
