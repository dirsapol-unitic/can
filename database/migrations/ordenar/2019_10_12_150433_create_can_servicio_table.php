<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCanServicioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('can_servicio', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('servicio_id')->unsigned();
            $table->integer('can_id')->unsigned();            
            $table->integer('establecimiento_id')->nullable();
            $table->integer('medicamento_cerrado')->default(1);
            $table->integer('dispositivo_cerrado')->default(1);
            $table->timestamps();
            $table->softDeletes();            
            $table->foreign('servicio_id')->references('id')->on('servicios');
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
        Schema::dropIfExists('can_servicio');
    }
}