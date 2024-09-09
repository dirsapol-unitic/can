<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFielsToUsersTable extends Migration
{
    
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dateTime('first_login')->nullable();
            $table->dateTime('last_login')->nullable();
            $table->dateTime('fin_last_login')->nullable();
            $table->dateTime('first_login_aplazado')->nullable();
            $table->dateTime('fin_last_login_aplazado')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
