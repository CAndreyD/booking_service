<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceSchedulesTable extends Migration
{
    public function up()
    {
        Schema::create('service_schedules', function (Blueprint $t) {
            $t->id();
            $t->foreignId('service_id')->constrained()->cascadeOnDelete();
            $t->tinyInteger('weekday'); 
            $t->time('start_time'); 
            $t->time('end_time');  
            $t->timestamps();
            $t->index(['service_id','weekday']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('service_schedules');
    }
}
