<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePetitorioRubroTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('petitorio_rubro', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('petitorio_id')->unsigned();
            $table->integer('rubro_id')->unsigned();
            $table->integer('tipo_dispositivo_medico_id')->default(1);
            $table->integer('medicamento_cerrado')->default(1);
            $table->integer('dispositivo_cerrado')->default(1);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('petitorio_id')->references('id')->on('petitorios');
            $table->foreign('rubro_id')->references('id')->on('rubros');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('petitorio_rubro');
    }
}
