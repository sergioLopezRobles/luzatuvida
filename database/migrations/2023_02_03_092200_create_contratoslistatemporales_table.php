<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class CreateContratosListaTemporalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contratoslistatemporales', function (Blueprint $table) {
            $table->increments('indice');
            $table->string("id")->nullable();
            $table->string("id_franquicia")->nullable();
            $table->string("estatus_estadocontrato")->nullable();
            $table->string("descripcion")->nullable();
            $table->string("idcontratorelacion")->nullable();
            $table->string("fechaentrega")->nullable();
            $table->string("fechaatraso")->nullable();
            $table->string("fechagarantia")->nullable();
            $table->string("estadogarantia")->nullable();
            $table->string("nombre_usuariocreacion")->nullable();
            $table->string("id_zona")->nullable();
            $table->string("zona")->nullable();
            $table->string("localidad")->nullable();
            $table->string("colonia")->nullable();
            $table->string("calle")->nullable();
            $table->string("numero")->nullable();
            $table->string("nombre")->nullable();
            $table->string("telefono")->nullable();
            $table->string("nombrereferencia")->nullable();
            $table->string("telefonoreferencia")->nullable();
            $table->string("totalreal")->nullable();
            $table->string("totalproducto")->nullable();
            $table->string("totalpromocion")->nullable();
            $table->string("totalabono")->nullable();
            $table->string("total")->nullable();
            $table->string("ultimoabono")->nullable();
            $table->string("promocionactiva")->nullable();
            $table->string("alias")->nullable();
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
        Schema::dropIfExists('contratoslistatemporales');
    }

}
