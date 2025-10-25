<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Create Admin User
        User::create([
            'name' => 'Admin N-Kitchen',
            'username' => 'admin',
            'email' => 'admin@nkitchen.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'phone' => '081234567890',
            'address' => 'Jl. Musi Palembang'
        ]);

        // Create Sample Customer
        User::create([
            'name' => 'Surya Guntur',
            'username' => 'surya',
            'email' => 'surya@gmail.com',
            'password' => Hash::make('surya'),
            'role' => 'customer',
            'phone' => '081987654321',
            'address' => 'Jl. Sudirman No. 123, Jakarta'
        ]);
    }
}