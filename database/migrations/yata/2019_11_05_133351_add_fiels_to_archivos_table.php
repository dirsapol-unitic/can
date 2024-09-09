<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;


class AddFielsToArchivosTable extends Migration
{
    
    public function up()
    {
        Schema::table('archivos', function (Blueprint $table) {
            $table->integer('servicio_id')->nullable();            
        });
    }
    
    public function down()
    {
        Schema::table('archivos', function (Blueprint $table) {
            //
        });
    }
}
