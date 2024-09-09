<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDivisionEstablecimientoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('division_establecimiento', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('division_id')->unsigned();
            $table->integer('establecimiento_id')->unsigned();
            $table->string('nombre_division')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('division_id')->references('id')->on('divisions');
            $table->foreign('establecimiento_id')->references('id')->on('establecimientos');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('division_establecimiento');
    }
}
