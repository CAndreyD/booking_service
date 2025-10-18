<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingsTable extends Migration
{
    public function up()
    {
        Schema::create('bookings', function (Blueprint $t) {
            $t->id();
            $t->foreignId('service_id')->constrained()->cascadeOnDelete();
            $t->timestamp('start_at'); 
            $t->timestamp('end_at');
            $t->string('client_name');
            $t->string('client_phone');
            $t->enum('status',['active','cancelled'])->default('active');
            $t->timestamps();

            $t->index(['service_id','start_at',]);
        });
    }

    public function down()
    {
        Schema::dropIfExists('bookings');
    }
}
