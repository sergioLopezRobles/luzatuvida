<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContratoscortellamadaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contratoscortellamada', function (Blueprint $table) {
            $table->increments('indice');
            $table->string("id_contrato")->nullable();
            $table->string("id_corte")->nullable();
            $table->string("id_historialcontrato")->nullable();
            $table->string("id_cobrador")->nullable();
            $table->string("tipo")->nullable();
            $table->string("ultimoabono")->nullable();
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
        Schema::dropIfExists('contratoscortellamada');
    }
}
