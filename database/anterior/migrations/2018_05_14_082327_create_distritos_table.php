<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDistritosTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('distritos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre_dist');
            $table->integer('provincia_id')->unsigned();
            $table->integer('departamento_id')->unsigned();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('provincia_id')->references('id')->on('provincias'); 
            $table->foreign('departamento_id')->references('id')->on('departamentos'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('distritos');
    }
}
