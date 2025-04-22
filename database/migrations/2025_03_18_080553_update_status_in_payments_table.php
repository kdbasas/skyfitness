<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateStatusInPaymentsTable extends Migration
{
    public function up()
    {
        // Update existing records to set status to 'Renewal' where applicable
        DB::table('payments')->where('status', 'Renew')->update(['status' => 'Renewal']);
    }

    public function down()
    {
        // Optionally, revert the status back to 'Renew' if needed
        DB::table('payments')->where('status', 'Renewal')->update(['status' => 'Renew']);
    }
}