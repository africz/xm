<?php

namespace Database\Seeders;

use Hash;
use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        User::truncate();
        $faker = \Faker\Factory::create();
        $password = Hash::make('xm');

        User::create([
            'name' => 'admin',
            'email' => 'admin@test.com',
            'password' => $password,
        ]);

        for ($i = 0; $i < 30; $i++) {
            User::create([
                'name' => $faker->name,
                'email' => $faker->email,
                'password' => $password,
            ]);
        }
    }
}
