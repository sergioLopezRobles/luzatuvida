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
        Schema::create('vehiculossupervision', function (Blueprint $table) {
            $table->increments('indice');
            $table->string("id_franquicia");
            $table->string("id_usuario")->nullable();
            $table->string("id_vehiculo")->nullable();
            $table->string("estado")->nullable();
            $table->string("kilometraje1")->nullable();
            $table->string("kilometraje2")->nullable();
            $table->string("ladoizquierdo")->nullable();
            $table->string("ladoderecho")->nullable();
            $table->string("frente")->nullable();
            $table->string("atras")->nullable();
            $table->string("extra1")->nullable();
            $table->string("extra2")->nullable();
            $table->string("extra3")->nullable();
            $table->string("extra4")->nullable();
            $table->string("extra5")->nullable();
            $table->string("extra6")->nullable();
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
        Schema::dropIfExists('vehiculossupervision');
    }
};
