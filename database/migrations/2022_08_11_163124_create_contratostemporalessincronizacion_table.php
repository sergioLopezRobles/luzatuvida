<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContratostemporalessincronizacionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contratostemporalessincronizacion', function (Blueprint $table) {
            $table->increments('indice');
            $table->string("id_usuario");
            $table->string("id")->nullable();
            $table->string("datos")->nullable();
            $table->string("id_usuariocreacion")->nullable();
            $table->string("nombre_usuariocreacion")->nullable();
            $table->string("id_zona")->nullable();
            $table->string("estatus")->nullable();
            $table->string("nombre")->nullable();
            $table->string("calle")->nullable();
            $table->string("numero")->nullable();
            $table->string("depto")->nullable();
            $table->string("alladode")->nullable();
            $table->string("frentea")->nullable();
            $table->string("entrecalles")->nullable();
            $table->string("colonia")->nullable();
            $table->string("localidad")->nullable();
            $table->string("telefono")->nullable();
            $table->string("telefonoreferencia")->nullable();
            $table->string("correo")->nullable();
            $table->string("nombrereferencia")->nullable();
            $table->string("casatipo")->nullable();
            $table->string("casacolor")->nullable();
            $table->string("fotoine")->nullable();
            $table->string("fotocasa")->nullable();
            $table->string("comprobantedomicilio")->nullable();
            $table->string("pagare")->nullable();
            $table->string("fotootros")->nullable();
            $table->string("pagosadelantar")->nullable();
            $table->string("id_optometrista")->nullable();
            $table->string("tarjeta")->nullable();
            $table->string("pago")->nullable();
            $table->string('abonominimo')->nullable();
            $table->string("id_promocion")->nullable();
            $table->string("fotoineatras")->nullable();
            $table->string("tarjetapensionatras")->nullable();
            $table->string("total")->nullable();
            $table->string("idcontratorelacion")->nullable();
            $table->string("contador")->nullable();
            $table->string("totalhistorial")->nullable();
            $table->string("totalpromocion")->nullable();
            $table->string("totalproducto")->nullable();
            $table->string("totalabono")->nullable();
            $table->string("fechaatraso")->nullable();
            $table->string("costoatraso")->nullable();
            $table->string("ultimoabono")->nullable();
            $table->string("estatus_estadocontrato")->nullable();
            $table->string("diapago")->nullable();
            $table->string("fechacobroini")->nullable();
            $table->string("fechacobrofin")->nullable();
            $table->string("enganche")->nullable();
            $table->string("entregaproducto")->nullable();
            $table->string("diaseleccionado")->nullable();
            $table->string("fechaentrega")->nullable();
            $table->string("promocionterminada")->nullable();
            $table->string("subscripcion")->nullable();
            $table->string("fechasubscripcion")->nullable();
            $table->string("nota")->nullable();
            $table->string("totalreal")->nullable();
            $table->string("diatemporal")->nullable();
            $table->string("coordenadas")->nullable();
            $table->string("nombrepaquete")->nullable();
            $table->string("ultimoabonoreal")->nullable();
            $table->string("titulopromocion")->nullable();
            $table->text('calleentrega')->nullable();
            $table->text('numeroentrega')->nullable();
            $table->text('deptoentrega')->nullable();
            $table->text('alladodeentrega')->nullable();
            $table->text('frenteaentrega')->nullable();
            $table->text('entrecallesentrega')->nullable();
            $table->text('coloniaentrega')->nullable();
            $table->text('localidadentrega')->nullable();
            $table->text('casatipoentrega')->nullable();
            $table->text('casacolorentrega')->nullable();
            $table->text('alias')->nullable();
            $table->text('autorizacion')->nullable();
            $table->dateTime('fecharegistro')->nullable();
            $table->text('opcionlugarentrega')->nullable();
            $table->text('observacionfotoine')->nullable();
            $table->text('observacionfotoineatras')->nullable();
            $table->text('observacionfotocasa')->nullable();
            $table->text('observacioncomprobantedomicilio')->nullable();
            $table->text('observacionpagare')->nullable();
            $table->text('observacionfotootros')->nullable();
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
        Schema::dropIfExists('contratostemporalessincronizacion');
    }
}
