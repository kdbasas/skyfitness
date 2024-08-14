<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    /**
     * Seed the application's database with the admin user.
     *
     * @return void
     */
    public function run()
    {
        // Check if there are no users before seeding
        if (DB::table('users')->where('email', 'admin@gmail.com')->doesntExist()) {
            DB::table('users')->insert([
                'name' => 'Karl Dave Basas',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('123'), // Replace with your desired password
                'profile_image' => 'dave.jpg',
                'role' => 'admin',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
