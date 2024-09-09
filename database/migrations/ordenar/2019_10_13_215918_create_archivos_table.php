<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateArchivosTable extends Migration
{
    public function up()
    {
        Schema::create('archivos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('can_id');
            $table->integer('establecimiento_id')->unsigned();                  
            $table->string('nombre_archivo');
            $table->string('descarga_archivo');       
            $table->string('extension_archivo');      
            $table->string('descripcion_archivo');
            $table->integer('user_id');        
            $table->integer('estado')->default(1);
            $table->timestamps();
            $table->softDeletes();            
            $table->foreign('establecimiento_id')->references('id')->on('establecimientos');
            $table->foreign('can_id')->references('id')->on('cans');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::drop('archivos');
    }
}
