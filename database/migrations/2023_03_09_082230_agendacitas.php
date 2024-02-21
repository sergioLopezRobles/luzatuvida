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
        Schema::create('agendacitas', function (Blueprint $table) {
            $table->increments('indice');
            $table->string('id_franquicia')->nullable();
            $table->string('nombre')->nullable();
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();
            $table->string('observaciones')->nullable();
            $table->string('fechacitaagendada')->nullable();
            $table->string('horacitaagendada')->nullable();
            $table->string('estadocita')->nullable();
            $table->string('localidad')->nullable();
            $table->string('colonia')->nullable();
            $table->string('domicilio')->nullable();
            $table->string('numero')->nullable();
            $table->string('entrecalles')->nullable();
            $table->string('lugarcita')->nullable();
            $table->string('tipocita')->nullable();
            $table->string('otrotipocita')->nullable();
            $table->string('referencia')->nullable();
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
        Schema::dropIfExists('agendacitas');
    }
};
