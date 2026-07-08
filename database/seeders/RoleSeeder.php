<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create(['name'=>'Admin',
        'email'=>'admin@gmail.com',
        'password'=>Hash::make('password'),
        'role'=>'admin'
        ]);

        User::create(['name'=>'User',
        'email'=>'user@gmail.com',
        'password'=>Hash::make('password'),
        'role'=>'user'
        ]);
    }
}
