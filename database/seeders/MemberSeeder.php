<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Member;         // Importing the Member model
use App\Models\Subscription;
use Endroid\QrCode\QrCode;
class MemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(SubscriptionSeeder::class);
        Member::factory()->count(50)->create();
        
    }
}
