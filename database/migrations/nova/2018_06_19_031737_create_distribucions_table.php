<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDistribucionsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('distribucions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('descripcion');
            $table->integer('establecimiento_id');
            $table->timestamps();
            $table->softDeletes();
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
        Schema::drop('distribucions');
    }
}
