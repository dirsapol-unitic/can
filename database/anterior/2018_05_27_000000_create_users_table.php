<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('dni')->unique();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->integer('establecimiento_id');
            $table->string('nombre_establecimiento');
            $table->integer('servicio_id')->default(0);
            $table->string('nombre_servicio')->nullable();            
            $table->integer('rol')->default(2);
            $table->string('nombres', 60);
            $table->string('apellidos', 60);
            $table->integer('grado_id');
            $table->string('grado');
            $table->string('telefono', 9);
            $table->rememberToken();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
