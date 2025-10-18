<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicesTable extends Migration
{
    public function up()
    {
        Schema::create('services', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->integer('duration_minutes');
            $t->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('services');
    }
}
