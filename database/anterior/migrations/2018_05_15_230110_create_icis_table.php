<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateIcisTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('icis', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('mes_id');
            $table->string('mes');
            $table->string('desc_mes');
            $table->integer('year_id');
            $table->string('ano');
            $table->string('anomes');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('icis');
    }
}



