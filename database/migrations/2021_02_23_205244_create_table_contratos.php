<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableContratos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contratos', function (Blueprint $table) {
            $table->increments('indice');
            $table->string('id');
            $table->boolean('datos')->default(0);
            $table->string('id_franquicia');
            $table->integer('id_usuariocreacion')->nullable();
            $table->string('nombre_usuariocreacion')->nullable();
            $table->integer('id_zona')->nullable();
            $table->integer('estatus')->default(0);
            $table->string('nombre')->nullable();
            $table->string('calle')->nullable();
            $table->string('numero')->nullable();
            $table->string('depto')->nullable();
            $table->string('alladode')->nullable();
            $table->string('frentea')->nullable();
            $table->string('entrecalles')->nullable();
            $table->string('colonia')->nullable();
            $table->string('localidad')->nullable();
            $table->string('telefono')->nullable();
            $table->string('telefonoreferencia')->nullable();
            $table->string('correo')->nullable();;
            $table->string('nombrereferencia')->nullable();
            $table->string('casatipo')->nullable();
            $table->string('casacolor')->nullable();
            $table->string('fotoine')->nullable();
            $table->string('fotocasa')->nullable();
            $table->string('comprobantedomicilio')->nullable();
            $table->string('pagare')->nullable();
            $table->string('fotootros')->nullable();
            $table->string('observaciones')->nullable();
            $table->string('nota')->nullable();
            $table->integer('pagosadelantar')->default(0);
            $table->string('banderacomentarioconfirmacion')->default("1");
            $table->string('estatusanteriorcontrato')->nullable();
            $table->string('diatemporal')->nullable();
            $table->string('coordenadas')->nullable();
            $table->dateTime('fecharegistro')->nullable();
            $table->string('opcionlugarentrega')->default("1");
            $table->string('observacionfotoine')->nullable();
            $table->string('observacionfotoineatras')->nullable();
            $table->string('observacionfotocasa')->nullable();
            $table->string('observacioncomprobantedomicilio')->nullable();
            $table->string('observacionpagare')->nullable();
            $table->string('observacionfotootros')->nullable();
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
        Schema::dropIfExists('contratos');
    }
}
