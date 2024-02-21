<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGarantiasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('garantias', function (Blueprint $table) {
            $table->increments('indice');
            $table->string('id','5');
            $table->string('id_contrato')->nullable();
            $table->string('id_historial')->nullable();
            $table->string('id_historialgarantia')->nullable();
            $table->string('id_optometrista')->nullable();
            $table->string('estadogarantia')->nullable();
            $table->string('estadocontratogarantia')->nullable();
            $table->string('totalhistorialcontratogarantia')->nullable();
            $table->string('totalpromocioncontratogarantia')->nullable();
            $table->string('totalrealcontratogarantia')->nullable();
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
        Schema::dropIfExists('garantias');
    }
}
