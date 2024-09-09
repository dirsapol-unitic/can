<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEstadoToEstimacionServicioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('estimacion_servicio', function (Blueprint $table) {
            $table->integer('estado')->default(0);   
            $table->integer('necesidad_anterior')->default(0);   
            $table->integer('cpma_anterior')->default(0);            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('estimacion_servicio', function (Blueprint $table) {
            $table->dropColumn('estado');  
            $table->dropColumn('necesidad_anterior');  
            $table->dropColumn('cpma_anterior');  
        });
    }
}
