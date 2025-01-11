<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\GymStaff;
use Illuminate\Database\Seeder;

class GymStaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        GymStaff::create([
            'email' => 'karl@gmail.com',
            'password' => bcrypt('password'),
            'first_name' => 'Karl',
            'middle_name' => '',
            'last_name' => 'Smith',
            'suffix_name' => '',
            'age' => 30,
            'contact_number' => '1234567890',
            'gender_id' => 1,
            'profile_image' => '',
            'role' => 'gym_staff',
        ]);
    }
}