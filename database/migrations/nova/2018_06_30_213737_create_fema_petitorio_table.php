<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFemaPetitorioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fema_petitorio', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('petitorio_id')->unsigned();
            $table->integer('establecimiento_id')->unsigned();
            $table->integer('tipo_dispositivo_medico_id')->default(1);
            $table->integer('medicamento_cerrado')->default(1);
            $table->integer('dispositivo_cerrado')->default(1);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('petitorio_id')->references('id')->on('petitorios');
            $table->foreign('establecimiento_id')->references('id')->on('femas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fema_petitorio');
    }
}
