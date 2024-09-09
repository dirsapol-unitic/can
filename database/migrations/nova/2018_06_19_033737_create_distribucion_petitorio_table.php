<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDistribucionPetitorioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('distribucion_petitorio', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('petitorio_id')->unsigned();
            $table->integer('distribucion_id')->unsigned();
            $table->integer('tipo_dispositivo_medico_id')->default(1);
            $table->integer('medicamento_cerrado')->default(1);
            $table->integer('dispositivo_cerrado')->default(1);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('petitorio_id')->references('id')->on('petitorios');
            $table->foreign('distribucion_id')->references('id')->on('distribucions');            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('distribucion_petitorio');
    }
}
