<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGymStaffTable extends Migration
{
    public function up()
    {
        Schema::create('gym_staff', function (Blueprint $table) {
            $table->id('gymstaff_id');
            $table->string('username')->unique();
            $table->string('password');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('suffix_name', 55)->default('');
            $table->integer('age');
            $table->string('email')->unique();
            $table->string('contact_number');
            $table->string('gender');
            $table->string('profile_image')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('gym_staff');
    }
}
