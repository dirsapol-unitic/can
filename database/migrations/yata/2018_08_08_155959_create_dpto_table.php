<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDptoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dpto', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('division_establecimiento_id')->unsigned();
            $table->integer('unidad_id')->unsigned();
            $table->string('nombre_departamento')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('unidad_id')->references('id')->on('unidads');
            $table->foreign('division_establecimiento_id')->references('id')->on('division_establecimiento');
            
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dpto');
    }
}
