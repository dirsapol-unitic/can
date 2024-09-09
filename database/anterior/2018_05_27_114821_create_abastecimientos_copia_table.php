<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAbastecimientosCopiaTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('abastecimientos_copia', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ici_id');
            $table->string('anomes');
            $table->integer('tipo_dispositivo_id');
            $table->integer('cod_establecimiento');
            $table->string('nombre_establecimiento');
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
        Schema::drop('abastecimientos_copia');
    }
}


/*
CREATE TABLE abastecimientos_copia
(
  id integer NOT NULL DEFAULT nextval('abastecimientos_copia_id_seq'::regclass),
  ici_id character varying(255) NOT NULL,
  anomes character varying(255) NOT NULL,
  tipo_dispositivo_id integer NOT NULL,
  cod_establecimiento integer NOT NULL,
  nombre_establecimiento character varying(255) NOT NULL,
  descripcion character varying(255) NOT NULL,
  petitorio_id integer NOT NULL,
  cod_petitorio integer NOT NULL,
  establecimiento_id integer NOT NULL,
  precio double precision NOT NULL DEFAULT '0'::double precision,
  cpma integer NOT NULL DEFAULT 0,
  stock_inicial integer NOT NULL DEFAULT 0,
  almacen_central integer NOT NULL DEFAULT 0,
  ingreso_proveedor integer NOT NULL DEFAULT 0,
  ingreso_transferencia integer NOT NULL DEFAULT 0,
  unidad_ingreso integer NOT NULL DEFAULT 0,
  valor_ingreso integer NOT NULL DEFAULT 0,
  unidad_consumo integer NOT NULL DEFAULT 0,
  valor_consumo integer NOT NULL DEFAULT 0,
  salida_transferencia integer NOT NULL DEFAULT 0,
  merma integer NOT NULL DEFAULT 0,
  total_salidas integer NOT NULL DEFAULT 0,
  stock_final integer NOT NULL DEFAULT 0,
  fecha_vencimiento character varying(255),
  disponibilidad integer NOT NULL DEFAULT 0,
  unidades_sobrestock integer NOT NULL DEFAULT 0,
  valor_sobrestock integer NOT NULL DEFAULT 0,
  created_at timestamp(0) without time zone,
  updated_at timestamp(0) without time zone,
  deleted_at timestamp(0) without time zone,
  CONSTRAINT abastecimientos_pkey PRIMARY KEY (id)
);
*/