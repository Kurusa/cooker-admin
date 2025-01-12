<?php

namespace Database\Seeders;

use App\Models\User;
use Faker\Generator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(Generator $faker): void
    {
        User::create([
            'name' => $faker->name,
            'email' => 'demo@demo.com',
            'password' => Hash::make('demo'),
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => $faker->name,
            'email' => 'admin@demo.com',
            'password' => Hash::make('demo'),
            'email_verified_at' => now(),
        ]);
    }
}
