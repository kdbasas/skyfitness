<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       // Migration for notifications table
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('member_id'); // Foreign key to members table
            $table->string('message');
            $table->enum('type', ['info', 'warning', 'danger']);
            $table->boolean('is_read')->default(false); // Whether the notification has been read
            $table->timestamps();
            $table->foreign('member_id')->references('member_id')->on('members')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};
