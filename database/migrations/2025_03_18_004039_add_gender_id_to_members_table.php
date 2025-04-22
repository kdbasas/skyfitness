<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGenderIdToMembersTable extends Migration
{
    public function up()
    {
        Schema::table('members', function (Blueprint $table) {
            $table->unsignedBigInteger('gender_id')->nullable(); // Add gender_id column

            // Add foreign key constraint
            $table->foreign('gender_id')->references('gender_id')->on('genders')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropForeign(['gender_id']); // Drop foreign key constraint
            $table->dropColumn('gender_id'); // Remove the gender_id column
        });
    }
}