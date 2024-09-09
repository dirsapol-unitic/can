<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCanDptoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('can_dpto', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('division_establecimiento_id')->unsigned();
            $table->integer('can_id')->unsigned();
            $table->integer('dpto_id')->unsigned();
            $table->integer('medicamento_cerrado')->default(1);
            $table->integer('dispositivo_cerrado')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('can_dpto');
    }
}