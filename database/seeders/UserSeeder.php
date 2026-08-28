<?php

namespace Database\Seeders;

use Hash;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate([
            'name' => 'System Admin',
            'email' => 'peterevarist36@gmail.com',
            'password' => Hash::make('BabaKai@123'),
            'username' => 'sysadmin'
        ]);
    }
}
