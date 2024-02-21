<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVacantesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vacantes', function (Blueprint $table) {
            $table->increments('indice');
            $table->string('id_franquicia')->nullable();
            $table->string('id_rol')->nullable();
            $table->text('observacionessolicitud')->nullable();
            $table->string('nombresolicitante')->nullable();
            $table->string('fechacita')->nullable();
            $table->string('horacita')->nullable();
            $table->string('telefono')->nullable();
            $table->text('observaciones')->nullable();
            $table->string('estado')->nullable();
            $table->string('identificador')->nullable();
            $table->string('curriculum')->nullable();
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
        Schema::dropIfExists('vacantes');
    }

}
