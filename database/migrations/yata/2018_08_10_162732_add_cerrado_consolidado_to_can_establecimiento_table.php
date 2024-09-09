<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCerradoConsolidadoToCanEstablecimientoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('can_establecimiento', function (Blueprint $table) {
            $table->integer('medicamento_cerrado_consolidado')->default(1);
            $table->integer('dispositivo_cerrado_consolidado')->default(1);
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
            $table->dropColumn('medicamento_cerrado_consolidado');
            $table->dropColumn('dispositivo_cerrado_consolidado');
        });
    }
}
