<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEspecialidadPetitorioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('especialidad_petitorio', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('petitorio_id')->unsigned();
            $table->integer('especialidad_id')->unsigned();
            $table->integer('tipo_dispositivo_medico_id')->default(1);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('petitorio_id')->references('id')->on('petitorios');
            $table->foreign('especialidad_id')->references('id')->on('especialidads');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('especialidad_petitorio');
    }
}
