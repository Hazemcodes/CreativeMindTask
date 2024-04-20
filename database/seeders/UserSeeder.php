<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'mobile_number' => '01159362182', 
            'username' => 'hazem',
            'user_type' => 'user',
            'password' => Hash::make('password123'), 
            'location' => 'Cairo',
            'isVerified' => true, 
        ]);

        User::create([
            'mobile_number' => '9876543210', 
            'username' => 'user2',
            'user_type' => 'delivery',
            'password' => Hash::make('password456'), 
            'location' => 'Giza',
            'isVerified' => false,
        ]);
    }
}
