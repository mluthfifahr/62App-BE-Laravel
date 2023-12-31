<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            EmployeePositionSeeder::class,
            EmployeeSeeder::class,
            UserSeeder::class,
            RoleSeeder::class,
            UserRoleSeeder::class,
            LeaveSeeder::class,
            ProjectSeeder::class,
            ProjectEmployeesSeeder::class,
        ]);

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
