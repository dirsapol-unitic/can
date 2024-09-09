<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateObservacionesTable extends Migration
{
    public function up()
    {
        Schema::create('observaciones', function (Blueprint $table) {
            $table->increments('id');            
            $table->integer('archivo_id');            
            $table->string('nombre_archivo');
            $table->string('descarga_archivo');       
            $table->string('extension_archivo');      
            $table->string('descripcion_archivo');
            $table->integer('user_id');        
            $table->integer('estado')->default(1);
            $table->timestamps();
            $table->softDeletes();            
            $table->foreign('archivo_id')->references('id')->on('archivos');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::drop('observaciones');
    }
}
