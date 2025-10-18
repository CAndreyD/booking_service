<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBookingsTableIndex extends Migration
{
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->index(['service_id', 'start_at', 'end_at'], 'bookings_service_time_index');
        });
    }

    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex('bookings_service_time_index');
        });
    }
}
