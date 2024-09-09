<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePetitoriosTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('petitorios', function (Blueprint $table) {
            $table->increments('id');            
            $table->integer('tipo_dispositivo_medicos_id')->unsigned()->nullable();
            $table->string('descripcion_tipo_dispositivo');
            $table->integer('codigo_petitorio');
            $table->string('principio_activo');
            $table->float('precio')->nullable();
            $table->string('concentracion')->nullable();            
            $table->string('form_farm')->nullable();
            $table->string('presentacion')->nullable();
            $table->integer('unidad_medida_id')->unsigned()->nullable();
            $table->string('descripcion_unidad_medida');
            $table->integer('nivel_id')->unsigned()->nullable();
            $table->string('descripcion_nivel');
            $table->integer('tipo_uso_id')->unsigned()->nullable();
            $table->string('descripcion_tipo_uso');
            $table->string('descripcion');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('tipo_dispositivo_medicos_id')->references('id')->on('tipo_dispositivo_medicos');
            $table->foreign('unidad_medida_id')->references('id')->on('unidad_medidas');
            $table->foreign('nivel_id')->references('id')->on('nivels');
            $table->foreign('tipo_uso_id')->references('id')->on('tipo_usos');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('petitorios');
    }
}
