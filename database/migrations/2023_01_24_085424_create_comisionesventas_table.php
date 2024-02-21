<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comisionesventas', function (Blueprint $table) {
            $table->increments('indice');
            $table->string('id_franquicia')->nullable();
            $table->string('usuario')->nullable();//0-> Asistente, 1-> Optometrista
            $table->string('totalcontratos')->nullable();
            $table->string('comision')->nullable();
            $table->string('valor')->nullable();
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
        Schema::dropIfExists('comisionesventas');
    }
};
