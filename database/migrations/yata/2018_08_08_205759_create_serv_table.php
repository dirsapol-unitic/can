<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('serv', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('dpto_id')->unsigned();
            $table->integer('servicio_id')->unsigned();            
            $table->string('codigo');
            $table->string('nombre_servicio');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('servicio_id')->references('id')->on('servicios');
            $table->foreign('dpto_id')->references('id')->on('dpto');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('serv');
    }
}
