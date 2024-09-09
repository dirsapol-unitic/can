<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateResponsablesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('responsables', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('can_id');
            $table->integer('establecimiento_id');
            $table->integer('servicio_id');
            $table->string('nombre_servicio')->nullable();   
            $table->string('nombre')->nullable();   
            $table->string('descripcion_grado')->nullable();   
            $table->string('celular')->nullable();   
            $table->string('nombre_establecimiento')->nullable();   
            $table->integer('rol');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('establecimiento_id')->references('id')->on('establecimientos');
            $table->foreign('can_id')->references('id')->on('cans'); 

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('responsables');
    }
}
