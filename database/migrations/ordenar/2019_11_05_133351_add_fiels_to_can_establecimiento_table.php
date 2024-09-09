<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFielsToCanEstablecimientoTable extends Migration
{
    
    public function up()
    {
        Schema::table('can_establecimiento', function (Blueprint $table) {
            $table->integer('rubro_pf')->nullable()->default(1);
            $table->integer('rubro_mb_iq_pa')->nullable()->default(1);
            $table->integer('rubro_mid')->nullable()->default(1);
            $table->integer('rubro_mil')->nullable()->default(1);
            $table->integer('rubro_mff')->nullable()->default(1);
        });
    }
    
    public function down()
    {
        Schema::table('can_establecimiento', function (Blueprint $table) {
            //
        });
    }
}
