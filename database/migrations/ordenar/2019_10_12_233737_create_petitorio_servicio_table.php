<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePetitorioServicioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('petitorio_servicio', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('petitorio_id')->unsigned();
            $table->integer('servicio_id')->unsigned();
            $table->integer('tipo_dispositivo_medico_id')->default(1);
            $table->integer('medicamento_cerrado')->default(1);
            $table->integer('dispositivo_cerrado')->default(1);
            $table->integer('uso_id')->unsigned();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('uso_id')->references('id')->on('tipo_usos');
            $table->foreign('petitorio_id')->references('id')->on('petitorios');
            $table->foreign('servicio_id')->references('id')->on('servicios');            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('petitorio_servicio');
    }
}
