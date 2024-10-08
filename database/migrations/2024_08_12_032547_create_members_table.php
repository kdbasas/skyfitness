<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMembersTable extends Migration
{
    public function up()
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id('member_id');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('suffix_name', 55)->nullable()->default('');
            $table->string('email')->unique();
            $table->string('contact_number');
            $table->unsignedBigInteger('subscription_id');
            $table->foreign('subscription_id')->references('subscription_id')->on('subscriptions')->onDelete('cascade');
            $table->decimal('amount', 8, 2);
            $table->date('date_joined');
            $table->date('date_expired')->nullable();
            $table->timestamps();

            $table->index('subscription_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('members');
    }
}
