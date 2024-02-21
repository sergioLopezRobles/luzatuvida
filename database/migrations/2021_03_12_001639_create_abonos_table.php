<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbonosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('abonos', function (Blueprint $table) {
            $table->increments('indice');
            $table->string('id');
            $table->string('folio')->nullable();
            $table->string("id_franquicia");
            $table->string("id_contrato");
            $table->string("id_usuario");
            $table->string("abono");
            $table->string("metodopago");
            $table->string("adelantos")->nullable();
            $table->string("tipoabono")->nullable();
            $table->string("atraso")->nullable();
            $table->string("poliza")->nullable();
            $table->string('corte')->default('2');
            $table->string("id_corte")->nullable();
            $table->string("id_contratoproducto")->nullable();
            $table->string("id_zona")->nullable();
            $table->dateTime('fecharegistro')->nullable();
            $table->string("coordenadas")->nullable();
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
        Schema::dropIfExists('abonos');
    }
}
