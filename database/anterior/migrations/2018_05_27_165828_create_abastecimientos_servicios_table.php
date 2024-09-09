<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAbastecimientosServiciosTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('abastecimientos_servicios', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ici_id');
            $table->string('anomes');
            $table->integer('tipo_dispositivo_id');
            $table->integer('cod_establecimiento');
            $table->string('nombre_establecimiento');
            $table->integer('servicio_id');
            $table->string('descripcion');
            $table->integer('petitorio_id')->unsigned();
            $table->integer('cod_petitorio');
            $table->integer('establecimiento_id')->unsigned();
            $table->double('precio')->default(0.0);
            $table->integer('cpma')->default(0);
            $table->integer('stock_inicial')->default(0);
            $table->integer('almacen_central')->default(0);            
            $table->integer('ingreso_proveedor')->default(0);          
            $table->integer('ingreso_transferencia')->default(0);            ;
            $table->integer('unidad_ingreso')->default(0);
            $table->double('valor_ingreso')->default(0.0);
            $table->integer('unidad_consumo')->default(0);
            $table->double('valor_consumo')->default(0.0);
            $table->integer('salida_transferencia')->default(0);            
            $table->integer('merma')->default(0);
            $table->integer('total_salidas')->default(0);
            $table->integer('stock_final')->default(0);
            $table->string('fecha_vencimiento')->nullable();
            $table->double('disponibilidad')->default(0.0);    
            $table->integer('unidades_sobrestock')->default(0);
            $table->double('valor_sobrestock')->default(0.0);
            $table->timestamps();
            $table->softDeletes();
            $table->integer('ingreso_almacen2')->default(0);          
            $table->foreign('petitorio_id')->references('id')->on('petitorios');
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
        Schema::drop('abastecimientos');
    }
}
