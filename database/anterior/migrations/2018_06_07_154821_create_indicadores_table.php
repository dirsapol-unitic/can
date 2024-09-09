<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateIndicadoresTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('indicadores', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ici_id');
            $table->string('anomes');
            $table->integer('region_id');
            $table->integer('nivel_id');
            $table->integer('categoria_id');
            $table->integer('tipo_establecimiento_id');
            $table->integer('establecimiento_id');
            $table->integer('normostock_cantidad')->default(0);
            $table->double('normostock_porcentaje')->default(0.0);
            $table->double('normostock_puntaje')->default(0.0);
            $table->integer('substock_cantidad')->default(0);
            $table->double('substock_porcentaje')->default(0.0);
            $table->double('substock_puntaje')->default(0.0);
            $table->integer('sobrestock_cantidad')->default(0);
            $table->double('sobrestock_porcentaje')->default(0.0);
            $table->double('sobrestock_puntaje')->default(0.0);
            $table->integer('sinrotacion_cantidad')->default(0);
            $table->double('sinrotacion_porcentaje')->default(0.0);
            $table->double('sinrotacion_puntaje')->default(0.0);
            $table->integer('desabastecido_cantidad')->default(0);
            $table->double('desabastecido_porcentaje')->default(0.0);
            $table->double('desabastecido_puntaje')->default(0.0);
            $table->integer('existente_cantidad')->default(0);
            $table->double('existente_porcentaje')->default(0.0);
            $table->double('existente_puntaje')->default(0.0);
            $table->integer('disponible_cantidad')->default(0);
            $table->double('disponible_porcentaje')->default(0.0);
            $table->double('disponible_puntaje')->default(0.0);
            $table->integer('total_items')->default(0);
            $table->double('total_puntaje')->default(0.0);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('ici_id')->references('id')->on('icis');
            $table->foreign('establecimiento_id')->references('id')->on('establecimientos');            
            $table->foreign('region_id')->references('id')->on('regions');
            $table->foreign('nivel_id')->references('id')->on('nivels');
            $table->foreign('tipo_establecimiento_id')->references('id')->on('tipo_establecimientos');
            $table->foreign('categoria_id')->references('id')->on('categorias');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('abastecimientos');
    }
}
