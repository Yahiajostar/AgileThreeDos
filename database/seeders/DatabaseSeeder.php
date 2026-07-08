<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Sprint;
use App\Models\Project;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Admin
        $admin = User::create([
            'name' => 'Yahia Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'plan' => 'premium'
        ]);

        // 2. Create Regular User
        $user = User::create([
            'name' => 'yahia ',
            'email' => 'yahiadiaa2017@gmail.com.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'plan' => 'free'
        ]);

        // 3. Create Project (with created_by and user_id from the diagram)
        $project = Project::create([
            'name' => 'Mobile App Redesign',
            'description' => 'Redesigning the company mobile application.',
            'status' => 'active',
            'start_date' => '2026-07-01',
            'end_date' => '2026-12-31',
            'created_by' => $admin->name, // matches varchar(255)
            'user_id' => $admin->id,      // matches bigint(20) unsigned
        ]);

        // 4. Create Sprint
        Sprint::create([
            'id'         => 16, 
            'name'       => 'Sprint 2 - UI Build',
            'description'=> 'Building the main UI components.',
            'status'     => 'active', 
            'start_date' => '2026-07-01', 
            'end_date'   => '2026-07-15', 
            'project_id' => $project->id,
        ]);

        $this->command->info('Test data seeded successfully!');
$this->call(RoleSeeder::class);
    }
}