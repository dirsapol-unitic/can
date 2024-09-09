<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateConsolidadosTable extends Migration
{
    public function up()
    {
        Schema::create('consolidados', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('can_id');
            $table->integer('establecimiento_id')->unsigned();
            $table->string('cod_establecimiento');
            $table->string('nombre_establecimiento');
            $table->integer('petitorio_id');                   
            $table->string('cod_petitorio')->nullable();
            $table->string('cod_siga')->nullable();
            $table->string('descripcion');
            $table->integer('tipo_dispositivo_id');            
            $table->integer('uso_id')->default(1);
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
            $table->integer('consolidado')->default(1);
            $table->double('disponibilidad')->default(0);
            $table->integer('requerimiento')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('petitorio_id')->references('id')->on('petitorios');
            $table->foreign('establecimiento_id')->references('id')->on('establecimientos');
            $table->foreign('can_id')->references('id')->on('cans');            
        });
    }

    public function down()
    {
        Schema::drop('consolidados');
    }
}
