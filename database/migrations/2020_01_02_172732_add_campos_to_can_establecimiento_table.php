<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCamposToCanEstablecimientoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('can_establecimiento', function (Blueprint $table) {
            $table->integer('medicamento_cerrado_stock')->default(1);   
            $table->integer('dispositivo_cerrado_stock')->default(1);        
            $table->timestamp('actualizar_stock')->useCurrent();               
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('can_establecimiento', function (Blueprint $table) {
            $table->dropColumn('medicamento_cerrado_stock');  
            $table->dropColumn('dispositivo_cerrado_stock');  
            $table->dropColumn('actualizar_stock');  
        });
    }
}
