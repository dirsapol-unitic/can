<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVistaEstablecimientoTotalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vista_establecimiento_total', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('can_id');
            $table->integer('petitorio_id');
            $table->string('cod_petitorio');            
            $table->string('descripcion');
            $table->float('precio')->nullable();
            $table->integer('tipo_dispositivo_id');            
            $table->integer('necesidad')->default(0);
            $table->float('valorizado')->default(0);
            $table->integer('establecimiento_1')->default(0);
            $table->integer('establecimiento_2')->default(0);
            $table->integer('establecimiento_3')->default(0);
            $table->integer('establecimiento_4')->default(0);
            $table->integer('establecimiento_5')->default(0);
            $table->integer('establecimiento_6')->default(0);
            $table->integer('establecimiento_7')->default(0);
            $table->integer('establecimiento_8')->default(0);
            $table->integer('establecimiento_9')->default(0);
            $table->integer('establecimiento_10')->default(0);
            $table->integer('establecimiento_11')->default(0);
            $table->integer('establecimiento_12')->default(0);
            $table->integer('establecimiento_13')->default(0);
            $table->integer('establecimiento_14')->default(0);
            $table->integer('establecimiento_15')->default(0);
            $table->integer('establecimiento_16')->default(0);
            $table->integer('establecimiento_17')->default(0);
            $table->integer('establecimiento_18')->default(0);
            $table->integer('establecimiento_19')->default(0);
            $table->integer('establecimiento_20')->default(0);
            $table->integer('establecimiento_21')->default(0);
            $table->integer('establecimiento_22')->default(0);
            $table->integer('establecimiento_23')->default(0);
            $table->integer('establecimiento_24')->default(0);
            $table->integer('establecimiento_25')->default(0);
            $table->integer('establecimiento_26')->default(0);
            $table->integer('establecimiento_27')->default(0);
            $table->integer('establecimiento_28')->default(0);
            $table->integer('establecimiento_29')->default(0);
            $table->integer('establecimiento_30')->default(0);
            $table->integer('establecimiento_31')->default(0);
            $table->integer('establecimiento_32')->default(0);
            $table->integer('establecimiento_33')->default(0);
            $table->integer('establecimiento_34')->default(0);
            $table->integer('establecimiento_35')->default(0);
            $table->integer('establecimiento_36')->default(0);
            $table->integer('establecimiento_37')->default(0);
            $table->integer('establecimiento_38')->default(0);
            $table->integer('establecimiento_39')->default(0);
            $table->integer('establecimiento_40')->default(0);
            $table->integer('establecimiento_41')->default(0);
            $table->integer('establecimiento_42')->default(0);
            $table->integer('establecimiento_43')->default(0);
            $table->integer('establecimiento_44')->default(0);
            $table->integer('establecimiento_45')->default(0);
            $table->integer('establecimiento_46')->default(0);
            $table->integer('establecimiento_47')->default(0);
            $table->integer('establecimiento_48')->default(0);
            $table->integer('establecimiento_49')->default(0);
            $table->integer('establecimiento_50')->default(0);
            $table->integer('establecimiento_51')->default(0);
            $table->integer('establecimiento_52')->default(0);
            $table->integer('establecimiento_53')->default(0);
            $table->integer('establecimiento_54')->default(0);
            $table->integer('establecimiento_55')->default(0);
            $table->integer('establecimiento_56')->default(0);
            $table->integer('establecimiento_57')->default(0);
            $table->integer('establecimiento_58')->default(0);
            $table->integer('establecimiento_59')->default(0);
            $table->integer('establecimiento_60')->default(0);
            $table->integer('establecimiento_61')->default(0);
            $table->integer('establecimiento_62')->default(0);
            $table->integer('establecimiento_63')->default(0);
            $table->integer('establecimiento_64')->default(0);
            $table->integer('establecimiento_65')->default(0);
            $table->integer('establecimiento_66')->default(0);
            $table->integer('establecimiento_67')->default(0);
            $table->integer('establecimiento_68')->default(0);
            $table->integer('establecimiento_69')->default(0);
            $table->integer('establecimiento_70')->default(0);
            $table->integer('establecimiento_71')->default(0);
            $table->integer('establecimiento_72')->default(0);
            $table->integer('establecimiento_73')->default(0);
            $table->integer('establecimiento_74')->default(0);
            $table->integer('establecimiento_75')->default(0);
            $table->integer('establecimiento_76')->default(0);
            $table->integer('establecimiento_77')->default(0);
            $table->integer('establecimiento_78')->default(0);
            $table->integer('establecimiento_79')->default(0);
            $table->integer('establecimiento_80')->default(0);
            $table->integer('establecimiento_81')->default(0);
            $table->integer('establecimiento_82')->default(0);
            $table->integer('establecimiento_83')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('petitorio_id')->references('id')->on('petitorios');
            $table->foreign('can_id')->references('id')->on('cans');            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vista_establecimiento_total');
    }
}