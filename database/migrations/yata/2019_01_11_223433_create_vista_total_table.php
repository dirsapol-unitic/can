<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVistaTotalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vista_total', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('can_id');
            $table->integer('petitorio_id');
            $table->integer('cod_petitorio');            
            $table->string('descripcion');
            $table->float('precio')->nullable();
            $table->integer('tipo_dispositivo_id');            
            $table->double('cpma')->default(0);
            $table->integer('stock')->default(0);
            $table->integer('necesidad_anual_nivel_1')->default(0);
            $table->integer('necesidad_anual_nivel_2')->default(0);
            $table->integer('necesidad_anual_nivel_3')->default(0);
            $table->integer('ajuste_necesidad_anual_nivel_2')->default(0);
            $table->integer('ajuste_necesidad_anual_nivel_3')->default(0);
            $table->integer('necesidad_total')->default(0);
            $table->integer('necesidad_total_ajuste')->default(0);
            $table->integer('necesidad_consolidado')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('petitorio_id')->references('id')->on('petitorios');
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
        Schema::dropIfExists('vista_total');
    }
}