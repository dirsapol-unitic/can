<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEstablecimientosTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('establecimientos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('codigo_establecimiento');
            $table->string('nombre_establecimiento');            
            $table->integer('region_id')->unsigned();
            $table->integer('nivel_id')->unsigned();
            $table->integer('categoria_id')->unsigned();
            $table->integer('tipo_establecimiento_id')->unsigned();
            $table->integer('tipo_internamiento_id')->unsigned();
            $table->integer('departamento_id')->unsigned();
            $table->integer('provincia_id')->unsigned();
            $table->integer('distrito_id')->unsigned();
            $table->integer('disa_id')->unsigned();
            $table->string('norte')->nullable();
            $table->string('este')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('region_id')->references('id')->on('regions');
            $table->foreign('nivel_id')->references('id')->on('nivels');
            $table->foreign('categoria_id')->references('id')->on('categorias');
            $table->foreign('tipo_establecimiento_id')->references('id')->on('tipo_establecimientos');
            $table->foreign('tipo_internamiento_id')->references('id')->on('tipo_internamientos');
            $table->foreign('departamento_id')->references('id')->on('departamentos');
            $table->foreign('provincia_id')->references('id')->on('provincias');
            $table->foreign('distrito_id')->references('id')->on('distritos');
            $table->foreign('disa_id')->references('id')->on('disas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('establecimientos');
    }
}
