<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFemasTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('femas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('can_id');
            $table->integer('establecimiento_id')->unsigned();
            $table->integer('cod_establecimiento');
            $table->string('nombre_establecimiento');
            $table->integer('petitorio_id');
            $table->integer('cod_petitorio');
            $table->string('descripcion');
            $table->integer('tipo_dispositivo_id');            
            $table->integer('cpma')->default(0);
            $table->integer('stock')->default(0);
            $table->double('necesidad_anual')->default(0);
            $table->integer('mes1')->default(0);
            $table->integer('mes2')->default(0);
            $table->integer('mes3')->default(0);
            $table->integer('mes4')->default(0);
            $table->integer('mes5')->default(0);
            $table->integer('mes6')->default(0);
            $table->integer('mes7')->default(0);
            $table->integer('mes8')->default(0);
            $table->integer('mes9')->default(0);
            $table->integer('mes10')->default(0);
            $table->integer('mes11')->default(0);
            $table->integer('mes12')->default(0);
            $table->string('justificacion')->nullable(); 
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('petitorio_id')->references('id')->on('petitorios');
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
        Schema::drop('femas');
    }
}
