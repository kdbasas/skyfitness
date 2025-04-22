<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('feedbacks', function (Blueprint $table) {
        $table->enum('status', ['unread', 'read'])->default('unread');
    });
}
};