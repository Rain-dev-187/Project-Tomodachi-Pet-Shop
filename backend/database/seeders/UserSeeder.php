<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get roles
        $adminRole = \App\Models\Role::where('name', 'admin')->first();
        $kasirRole = \App\Models\Role::where('name', 'kasir')->first();
        $ownerRole = \App\Models\Role::where('name', 'owner')->first();

        // Create admin user
        User::firstOrCreate(
            ['email' => 'admin@petshop.local'],
            [
                'name' => 'Administrator',
                'email' => 'admin@petshop.local',
                'password' => Hash::make('password'),
                'role_id' => $adminRole?->id,
            ]
        );

        // Create kasir user
        User::firstOrCreate(
            ['email' => 'kasir@petshop.local'],
            [
                'name' => 'Kasir',
                'email' => 'kasir@petshop.local',
                'password' => Hash::make('password'),
                'role_id' => $kasirRole?->id,
            ]
        );

        // Create owner user
        User::firstOrCreate(
            ['email' => 'owner@petshop.local'],
            [
                'name' => 'Owner',
                'email' => 'owner@petshop.local',
                'password' => Hash::make('password'),
                'role_id' => $ownerRole?->id,
            ]
        );
    }
}
