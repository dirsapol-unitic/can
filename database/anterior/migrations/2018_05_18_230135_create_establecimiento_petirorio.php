<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEstablecimientoPetirorio extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('establecimiento_petitorio', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('establecimiento_id')->unsigned();
            $table->integer('petitorio_id')->unsigned();
            $table->integer('tipo_dispositivo_medico_id')->default(1);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('establecimiento_id')->references('id')->on('establecimientos');
            $table->foreign('petitorio_id')->references('id')->on('petitorios');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::drop('establecimiento_petitorio');
    }
}
