<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('gym_staffs', function (Blueprint $table) {
            $table->string('role')->default('gym_staff'); // Add the role column with a default value
        });
    }

    public function down()
    {
        Schema::table('gym_staffs', function (Blueprint $table) {
            $table->dropColumn('role'); // Remove the role column if rolling back
        });
    }
};