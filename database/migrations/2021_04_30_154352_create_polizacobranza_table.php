<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePolizacobranzaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('polizacobranza', function (Blueprint $table) {
            $table->id();
            $table->string('id_usuario')->nullable();
            $table->string('id_franquicia');
            $table->string('id_poliza');
            $table->string('fechapoliza');
            $table->string('nombre')->nullable();
            $table->string('zona');
            $table->string('tabular');
            $table->string('archivo');
            $table->string('pagadas');
            $table->string('garantias')->nullable();
            $table->string('cobradas');
            $table->string('acumuladasemana');
            $table->string('diarioacumulado');
            $table->string('promedioacumulado')->nullable();
            $table->string('gas');
            $table->string('ingresocobranza');
            $table->string('ingresooficina');
            $table->string('ingresoacumulado');
            $table->string('tarjetassietecinco')->nullable();
            $table->string('tarjetascobrarsietecinco')->nullable();
            $table->string('cobrarsietecinco')->nullable();
            $table->string('tarjetasochenta')->nullable();
            $table->string('tarjetascobrarochenta')->nullable();
            $table->string('cobrarochenta')->nullable();
            $table->string('sueldo');
            $table->string('seissietecinco')->nullable();
            $table->string('ochosietecinco')->nullable();
            $table->string('totalpagar');
            $table->string('id_zona')->nullable();
            $table->string('ingresosupervisor')->default("0");
            $table->string('ingresousuarioseliminados')->default("0");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('polizacobranza');
    }
}
