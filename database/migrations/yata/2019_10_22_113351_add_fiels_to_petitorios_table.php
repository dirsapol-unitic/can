<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFielsToPetitoriosTable extends Migration
{
    
    public function up()
    {
        Schema::table('petitorios', function (Blueprint $table) {
            $table->string('descripcion_siga')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('petitorios', function (Blueprint $table) {
            //
        });
    }
}
